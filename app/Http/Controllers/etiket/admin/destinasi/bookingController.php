<?php

namespace App\Http\Controllers\etiket\admin\destinasi;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\helper\BookingHelperController;
use App\Mail\BookingPayment;
use App\Mail\BookingPaymentFailed;
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

        // Query utama
        $query = gk_booking::where('gk_bookings.status_booking', '>=', 3)
            ->whereHas('destinasi', function ($q) use ($destinasi) {
                $q->where('destinasis.id', $destinasi->id);
            });

        // 🔹 **Filter berdasarkan status waktu booking**
        if ($request->filled('filter-waktu')) {
            if ($request->input('filter-waktu') === 'dalam_booking') {
                $query->where(function ($q) {
                    $q->where(function ($q1) {
                        // ✅ Kondisi 1: Status booking antara 4 dan 7 & tanggal masuk sudah lewat
                        $q1->whereBetween('gk_bookings.status_booking', [4, 7])
                            ->whereDate('gk_bookings.tanggal_masuk', '<', now());
                    })->orWhere(function ($q2) {
                        // ✅ Kondisi 2: Status booking = 3 & pembayaran terbaru masih pending
                        $q2->where('gk_bookings.status_booking', 3)
                            ->whereHas('pembayaran', function ($q3) {
                                $q3->where('status', 'pending')
                                    ->orderByDesc('created_at') // Ambil pembayaran terbaru
                                    ->limit(1);
                            });
                    });
                });
            } elseif ($request->input('filter-waktu') === 'sudah_selesai') {
                $query->where('gk_bookings.status_booking', '=', 8);
            } elseif ($request->input('filter-waktu') === 'akan_datang') {
                $query->whereDate('gk_bookings.tanggal_masuk', '>', now())
                    ->whereDoesntHave('pembayaran', function ($q) {
                        $q->where('status', 'pending')->orderByDesc('created_at')->limit(1);
                    }); // Pastikan tidak ada pembayaran pending
            }
        }


        // 🔹 **Filter berdasarkan pencarian (Nama atau Email)**
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

        // 🔹 **Optimasi Urutan Data**
        $query->orderByRaw("
                CASE 
                    WHEN EXISTS (
                        SELECT 1 FROM pembayarans 
                        WHERE pembayarans.id_booking = gk_bookings.id 
                        AND pembayarans.status = 'pending'
                    ) THEN 0 
                    -- Jika ada pembayaran pending, letakkan paling atas

                    WHEN gk_bookings.tanggal_keluar <= CURDATE() AND gk_bookings.status_booking < 8 THEN 2
                    -- Prioritas 2: Booking yang sudah melewati tanggal_keluar tetapi belum selesai

                    ELSE 3
                END
            ")
            ->orderByRaw("  
                CASE 
                    WHEN tanggal_masuk = CURDATE() THEN 1   
                    WHEN tanggal_masuk > CURDATE() THEN 2  
                    ELSE 3  
                END
            ")
            ->orderBy('tanggal_masuk', 'asc')
            ->orderByDesc(function ($subQuery) {
                $subQuery->select('created_at')
                    ->from('pembayarans')
                    ->whereColumn('pembayarans.id_booking', 'gk_bookings.id')
                    ->latest()
                    ->take(1);
            });


        // Ambil data dengan paginasi
        $data = $query->paginate(20);


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
                Log::channel('admin')->error('Gagal mengirim email konfirmasi pembayaran.', [
                    'error' => $e->getMessage(),
                    'user_email' => $userBooking->email,
                    'booking_code' => $booking->unique_code,
                    'amount' => $booking->total_pembayaran,
                    'payment_date' => $booking->pembayaran->last()->created_at,
                    'invoice_url' => route('homepage.booking.struk', $booking->id),
                ]);
            }
        } else {
            $booking->update([
                'unique_code' => null,
                'status_booking' =>  3,
                'status_pembayaran' =>  0,
            ]);

            // Kirim email untuk pembayaran gagal
            $failedOrder = [
                'name' => $userBooking->biodata->first_name . ' ' . $userBooking->biodata->last_name,
                'booking_code' => $booking->id,
                'amount' => $booking->total_pembayaran,
                'payment_status' => 'Failed',
                'email' => $userBooking->email,
            ];

            try {
                Mail::to($userBooking->email)->send(new BookingPaymentFailed($failedOrder));
            } catch (\Exception $e) {
                Log::channel('admin')->error('Gagal mengirim email pembayaran gagal.', [
                    'error' => $e->getMessage(),
                    'user_email' => $userBooking->email,
                    'booking_code' => $booking->id,
                    'amount' => $booking->total_pembayaran,
                    'payment_status' => 'Failed',
                ]);
            }
        }

        return redirect()->back()->with('success', 'Pengajuan berhasil diperbarui');
    }

    public function showBooking($id)
    {
        $booking = gk_booking::with(['gateMasuk', 'gateKeluar', 'pendakis.biodata.user', 'pendakis.getStatus',  'destinasi'])->where('id', $id)->first();


        $listStatusPendakian = $booking->riwayatPendakian();


        // return $listStatusPendakian[0];
        return view('etiket.admin.destinasi.booking.showBooking', [
            'booking' => $booking,
            'listStatusPendakian' => $listStatusPendakian,
        ]);
    }

    public function gantiTanggal(Request $request)
    {
        $request->validate([
            'id_booking' => 'required|uuid|exists:gk_bookings,id',
            'totalHari' => 'required|integer|min:1',
            'bookinfStartDate' => 'required|date',
        ]);
        $booking = gk_booking::where('id', $request->id_booking)
            ->where('status_booking', '>=', 4)
            ->first();

        if (!$booking) {
            return redirect()->back()->withErrors('Booking tidak ditemukan');
        }

        // cek booking status blm ada pendakian
        if ($booking->status_booking > 5) {
            return redirect()->back()->withErrors('Booking sudah ada pendakian');
        }

        $endDate = Carbon::parse($request->bookinfStartDate)->addDays($booking->total_hari);
        $booking->update([
            'tanggal_masuk' => $request->bookinfStartDate,
            'tanggal_keluar' => $endDate,
        ]);

        return redirect()->back()->with('success', 'Tanggal berhasil diubah');
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
            'name' => 'required|integer|in:0,1,2,3,4',
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
                    $message += $this->bookingSelesai($booking);
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
            $booking->update(['status_booking' => 8]);
            return ['success' => ['Status booking berhasil diubah menjadi selesai']];
        }
        return ['error' => ['Tidak semua pendaki selesai']];
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
            'data' => $booking
        ]);
    }
}
