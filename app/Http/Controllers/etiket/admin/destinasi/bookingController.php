<?php

namespace App\Http\Controllers\etiket\admin\destinasi;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\helper\BookingHelperController;
use App\Mail\BookingPayment;
use App\Models\destinasi;
use App\Models\gk_booking;
use App\Models\statusPendaki;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

use function PHPUnit\Framework\isEmpty;

class bookingController extends AdminController
{
    private $helper;

    public function __construct(BookingHelperController $helper)
    {
        $this->helper = $helper;
    }

    public function index($id, Request $request)
    {
        $destinasi = destinasi::find($id);

        $query = gk_booking::with('pembayaran', 'pendakis', 'pendakis.biodata', 'user', 'destinasi')
            ->whereHas('destinasi', function ($q) use ($destinasi) {
                $q->where('destinasis.id', $destinasi->id);
            });

        // Filter berdasarkan status
        if ($request->filled('filter')) {
            $query->whereHas('pembayaran', function ($q) use ($request) {
                $q->where('status', $request->filter);
            });
        }

        if ($request->filled('search')) {
            $query->whereHas('pendakis.biodata', function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                    ->orWhere('last_name', 'like', '%' . $request->search . '%');
            })
                ->orWhereHas('user', function ($q) use ($request) {
                    $q->where('email', 'like', '%' . $request->search . '%');
                })
                ->orWhereDoesntHave('pendakis'); // Menyertakan booking tanpa pendakis
        }

        // Filter berdasarkan rentang tanggal created_at
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $query->where('status_booking', '>=', 3);


        // Tambahkan pengurutan berdasarkan created_at dari relasi pembayaran
        $query->orderByDesc(function ($subQuery) {
            $subQuery->select('created_at')
                ->from('pembayarans')
                ->whereColumn('pembayarans.id_booking', 'gk_bookings.id')
                ->latest()
                ->take(1);
        });
        // Ambil data dengan paginasi
        $data = $query->paginate(10);


        return view('etiket.admin.destinasi.booking.index', [
            'destinasi' => $destinasi,
            'data' => $data
        ]);
    }

    public function showPembayaran($id)
    {
        $booking = gk_booking::with('pendakis.biodata', 'user', 'pembayaran')
            ->where('id', $id)
            ->first();
        // return $booking;

        return view('etiket.admin.destinasi.booking.showPembayaran', [
            'booking' => $booking
        ]);
    }

    public function updatePembayaran(Request $request)
    {
        $request->validate([
            'id_booking' => 'required|string',
            'keterangan' => 'required|string|nullable|max:255',
            'verified' => 'required|in:yes,no',
        ]);

        $booking = gk_booking::with('pembayaran')->findOrFail($request->id_booking);
        $userBooking = User::with('biodata')->find($booking->id_user);
        if (!$booking) {
            abort(404);
        }

        if ($booking->status_booking > 4) {
            return redirect()->back()->withErrors('Bookingan sudah melakukan pendakian');
        }

        // Perbarui keterangan pembayaran terakhir
        if ($booking->pembayaran->isNotEmpty()) {
            $pembayaranPending = $booking->pembayaran->where('status', 'pending');
            foreach ($pembayaranPending as  $p) {
                $p->update([
                    'status' => $request->verified === 'yes' ? 'success' : 'failed',
                ]);
            }
            $lastPembayaran = $booking->pembayaran->last();
            $lastPembayaran->update([
                'status' => $request->verified === 'yes' ? 'success' : 'failed',
                'keterangan' => $request->keterangan,
            ]);
        } else {
            return redirect()->back()->withErrors('Pembayaran tidak ditemukan');
        }

        if ($request->verified === 'yes') {
            $booking->update([
                'unique_code' =>  $this->helper->generateCode(10),
                'status_booking' =>  4,
                'status_pembayaran' =>  1,
            ]);

            // update struk
            $booking->load('pembayaran');
            $dataStruk = json_decode($booking->dataStruk);

            $dataStruk->status_booking = 4;
            $dataStruk->status_pembayaran = 1;
            $dataStruk->unique_code = $booking->unique_code;
            $dataStruk->pembayaran = $booking->pembayaran;

            $booking->update([
                'dataStruk' => json_encode($dataStruk),
            ]);

            // Kirim email ke user
            $order = [
                'name' => $userBooking->biodata->first_name . ' ' . $userBooking->biodata->last_name,
                'booking_code' => $booking->unique_code,
                'amount' => $booking->total_pembayaran,
                'payment_date' => $booking->pembayaran->last()->created_at,
                'invoice_url' => route('homepage.booking.struk', $booking->id),
                'email' => $userBooking->email,
            ];
            try {
                Mail::to($userBooking->email)->send(new BookingPayment($order));
            } catch (\Exception $e) {
                Log::channel('admin')->error(
                    'Terjadi kesalahan pada proses booking kirim email pembelian booking ke ' . $userBooking->email,
                    [
                        'admin' => Auth::user(),
                        'pengguna' => $userBooking,
                        'error' => $e->getMessage()
                    ]
                );
            }
        } else {
            $booking->update([
                'unique_code' => null,
                'status_booking' =>  3,
                'status_pembayaran' =>  0,
            ]);
        }

        return redirect()->back()->with('success', 'Pengajuan berhasil diperbarui');
    }

    public function showBooking($id)
    {
        $booking = gk_booking::with(['gateMasuk', 'gateKeluar', 'pendakis.biodata.user', 'pendakis.getStatus', 'destinasi'])->where('id', $id)->first();

        $listStatusPendakian = $booking->pendakis->flatMap(function ($pendaki) {
            return collect($pendaki->getStatus)->map(function ($status) use ($pendaki) {
                return (object) [
                    'id' => $status->id,
                    'status' => $status->status,
                    'statusName' => $status->statusName(),
                    'id_pendaki' => $status->id_pendaki,
                    'detail' => $status->detail,
                    'tanggal' => Carbon::parse($status->created_at)->format('Y-m-d'), // Format tanggal
                    'jam' => Carbon::parse($status->created_at)->format('H:i:s'), // Format jam
                    'fullName' => $pendaki->fullName, // Menambahkan nama pendaki
                ];
            });
        });


        // return $listStatusPendakian[0];
        return view('etiket.admin.destinasi.booking.showBooking', [
            'booking' => $booking,
            'listStatusPendakian' => $listStatusPendakian,
        ]);
    }

    public function showTiket($id)
    {
        $booking = gk_booking::with(['gateMasuk', 'gateKeluar', 'pendakis.biodata', 'destinasi'])->where('id', $id)->first();

        return view('etiket.admin.destinasi.booking.showTiket', [
            'booking' => $booking
        ]);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|uuid|exists:gk_bookings,id',
            'name' => 'required|integer|in:0,1,2,3',
            'pendakis' => 'nullable|array',
            'pendakis.*' => 'nullable|in:0,1'
        ]);

        // return $request;

        // cek status booking >= 4
        $booking = gk_booking::find($request->booking_id);
        if ($booking->status_booking < 4 and $booking->status_booking > 8) {
            return redirect()->back()->withErrors('Bookingan belum menyelesaikan pembayaran');
        }

        $message = [];

        // jika tanggal masuk < hari ini, cuma bisa memberi konfirmasi batal pendakian
        if ($booking->tanggal_masuk > date('Y-m-d')) {
            if ($request->name != 0) {
                return redirect()->back()->withErrors('Tidak bisa mengubah status booking');
            }
            $message += $this->pendakiCancel($booking, $request->pendakis);
        } else {
            switch ($request->name) {
                case '1':
                    $message += $this->pendakiCancel($booking, $request->pendakis);
                    break;
                case '2':
                    $message += $this->pendakiCekIn($booking, $request->pendakis);
                    break;
                case '3':
                    $message += $this->pendakiCekOut($booking, $request->pendakis);
                    break;
                case '4':
                    $this->bookingSelesai($booking);
                    break;

                default:
                    return redirect()->back()->withErrors('Tidak bisa mengubah status booking');
                    break;
            }
        }


        return redirect()->back()->with($message);
    }

    private function bookingSelesai($booking)
    {
        $booking->load('pendakis.getStatus');
        // Cek jika semua pendaki dalam booking memiliki status terakhir 3 (Cek Out) atau 1 (Batal Mendaki)
        $semuaSelesai = $booking->pendakis->every(function ($pendaki) {
            $lastStatus = $pendaki->getStatus->last();
            return $lastStatus && in_array($lastStatus->status, [1, 3]);
        });

        // Jika semua pendaki sudah selesai (Cek Out atau Batal), update status booking menjadi 7
        if ($semuaSelesai) {
            $booking->update(['status_booking' => 7]);
        }
    }

    private function pendakiCancel($booking, $pendakis)
    {
        $booking->load('pendakis.getStatus');
        $messages = [];
        foreach ($pendakis as $idPendaki => $statusPendaki) {
            $pendaki = $booking->pendakis->where('id', $idPendaki)->first();
            $lastStatus = $pendaki->getStatus->last();
            if (empty($lastStatus)) {
                if ($statusPendaki == 1) {
                    statusPendaki::create([
                        'id_pendaki' => $pendaki->id,
                        'status' => 1,
                        'detail' => 'Batal melakukan pendakian',
                    ]);
                    $messages['error'][] = 'Status pendakian ' . $pendaki->fullName . ' dibatalkan';
                }
            } elseif ($lastStatus->status == 1) {
                if ($statusPendaki == 0) {
                    $lastStatus->delete();
                }
                $messages['success'][] = 'Status pendakian ' . $pendaki->fullName . ' berhasil diupdate';
            } elseif ($lastStatus->status > 1) {
                if ($statusPendaki == 1) {
                    $messages['error'][] = 'Status pendakian ' . $pendaki->fullName . ' tidak dapat dibatalkan, karna sudah ' . $lastStatus->statusName();
                }
            }
        }

        if ($booking->status_booking == 4) {
            $booking->update([
                'status_booking' => 5
            ]);
        }

        return $messages;
    }
    private function pendakiCekIn($booking, $pendakis)
    {
        $booking->load('pendakis.getStatus');
        $messages = [];
        foreach ($pendakis as $idPendaki => $statusPendaki) {
            $pendaki = $booking->pendakis->where('id', $idPendaki)->first();
            $lastStatus = $pendaki->getStatus->last();

            if (empty($lastStatus)) {
                if ($statusPendaki == 1) {
                    statusPendaki::create([
                        'id_pendaki' => $pendaki->id,
                        'status' => 2,
                        'detail' => 'Melakukan pendakian',
                    ]);
                    $messages['success'][] = 'Status pendakian ' . $pendaki->fullName . ' berhasil diupdate';
                }
            } elseif ($lastStatus->status == 1) {
                $messages['error'][] = 'Status pendakian ' . $pendaki->fullName . ' sudah dibatalkan';
            } elseif ($lastStatus->status == 2) {
                if ($statusPendaki == 0) {
                    $lastStatus->delete();
                    $messages['success'][] = 'Status pendakian ' . $pendaki->fullName . ' berhasil dihapus';
                }
            } elseif ($lastStatus->status == 3) {
                if ($statusPendaki == 0) {
                    $messages['error'][] = 'Status pendakian ' . $pendaki->fullName . ' tidak dapat diterima, karna sudah ' . $lastStatus->statusName();
                }
            }
        }

        if ($booking->status_booking <= 5) {
            $booking->update([
                'status_booking' => 6
            ]);
        }

        return $messages;
    }
    private function pendakiCekOut($booking, $pendakis)
    {
        $booking->load('pendakis.getStatus');
        $messages = [];
        foreach ($pendakis as $idPendaki => $statusPendaki) {
            $pendaki = $booking->pendakis->where('id', $idPendaki)->first();
            $lastStatus = $pendaki->getStatus->last();

            if (empty($lastStatus)) {
                if ($statusPendaki == 1) {
                    $messages['error'][] = 'Status pendakian ' . $pendaki->fullName . ' gagal diupdate';
                }
            } elseif ($lastStatus->status == 1) {
                $messages['error'][] = 'Status pendakian ' . $pendaki->fullName . ' tidak dapat diterima, karna sudah ' . $lastStatus->statusName();
            } elseif ($lastStatus->status == 2) {
                if ($statusPendaki == 1) {
                    statusPendaki::create([
                        'id_pendaki' => $pendaki->id,
                        'status' => 3,
                        'detail' => 'Melakukan pendakian',
                    ]);
                    $messages['success'][] = 'Status pendakian ' . $pendaki->fullName . ' berhasil diupdate';
                }
            } elseif ($lastStatus->status == 3) {
                if ($statusPendaki == 0) {
                    $lastStatus->delete();
                    $messages['success'][] = 'Status pendakian ' . $pendaki->fullName . ' berhasil dihapus';
                } else {
                    $messages['success'][] = 'Status pendakian ' . $pendaki->fullName . ' sudah ' . $lastStatus->statusName();
                }
            }
        }


        $booking->refresh();
        $semuaSelesai = $booking->pendakis->every(function ($pendaki) {
            $lastStatus = $pendaki->getStatus->last();
            return $lastStatus && in_array($lastStatus->status, [1, 3]);
        });

        // Jika semua pendaki sudah selesai (Cek Out atau Batal), update status booking menjadi 7
        if ($semuaSelesai) {
            $booking->update(['status_booking' => 7]);
        } else {
            $booking->update(['status_booking' => 6]);
        }

        return $messages;
    }


    public function showStruk($id)
    {
        $booking = gk_booking::where('id', $id)->first();

        if ($booking->status_pembayaran) {
            $booking = json_decode($booking->dataStruk);
            // return $booking;
        } else {
            $booking = $this->helper->getDataStruk($booking->id);
        }

        // return $booking;

        return view('etiket.admin.destinasi.booking.showStruk', [
            'booking' => $booking
        ]);
    }
}
