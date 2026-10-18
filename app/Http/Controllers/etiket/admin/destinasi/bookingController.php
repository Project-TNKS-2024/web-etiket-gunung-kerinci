<?php

namespace App\Http\Controllers\etiket\admin\destinasi;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\helper\BookingHelperController;
use App\Mail\BookingPayment;
use App\Mail\BookingPaymentFailed;
use App\Models\destinasi;
use App\Models\gk_booking;
use App\Models\gk_tiket_pendaki;
use App\Models\GkPendakiTarifSnapshot;
use App\Models\GkOverstayCharge;
use App\Models\statusPendaki;
use App\Models\User;
use App\Services\AdditionalChargePaymentService;
use App\Services\CheckoutService;
use App\Services\OverstayCalculator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

use function PHPUnit\Framework\isEmpty;

class bookingController extends AdminController
{
    private $helper;
    private CheckoutService $checkout;
    private AdditionalChargePaymentService $paymentService;
    private OverstayCalculator $overstay;

    public function __construct(
        BookingHelperController $helper,
        CheckoutService $checkout,
        AdditionalChargePaymentService $paymentService,
        OverstayCalculator $overstay
    ) {
        $this->helper = $helper;
        $this->checkout = $checkout;
        $this->paymentService = $paymentService;
        $this->overstay = $overstay;
    }

    public function index($id, Request $request)
    {
        $destinasi = destinasi::find($id);

        // Query utama
        $queryUtama = gk_booking::with(['pendakis.biodata', 'gateMasuk', 'pembayaran'])
            ->whereHas('destinasi', function ($q) use ($destinasi) {
                $q->where('destinasis.id', $destinasi->id);
            });

        // 🔹 **Filter berdasarkan status waktu booking**
        if ($request->filled('filter-waktu')) {
            if ($request->input('filter-waktu') === 'dalam_booking') {
                $queryUtama->where(function ($q) {
                    $q->whereBetween('gk_bookings.status_booking', [4, 6])
                        ->whereDate('gk_bookings.tanggal_masuk', '<=', now());
                });
            } elseif ($request->input('filter-waktu') === 'sudah_selesai') {
                $queryUtama->whereIn('gk_bookings.status_booking', [7, 8]);
            } elseif ($request->input('filter-waktu') === 'dibatalkan') {
                $queryUtama->where('gk_bookings.status_booking', gk_booking::STATUS_CANCELLED);
            } elseif ($request->input('filter-waktu') === 'draft') {
                $queryUtama->whereBetween('gk_bookings.status_booking', [0, 3]);
            } elseif ($request->input('filter-waktu') === 'akan_datang') {
                $queryUtama->whereBetween('gk_bookings.status_booking', [4, 6])->whereDate('gk_bookings.tanggal_masuk', '>', now());
            }
        }

        // 🔹 **Filter keterlambatan tiket (overstay)**
        if ($request->filled('filter-overstay')) {
            if ($request->input('filter-overstay') === 'overdue') {
                $today = now()->setTimezone('Asia/Jakarta')->toDateString();
                $queryUtama->whereDate('gk_bookings.tanggal_keluar', '<', $today)
                    ->whereHas('pendakis', function ($q) {
                        $q->whereHas('getStatus', fn ($s) => $s->where('status', 2))
                            ->whereDoesntHave('getStatus', fn ($s) => $s->where('status', 3));
                    });
            } elseif ($request->input('filter-overstay') === 'unpaid') {
                $queryUtama->whereHas('pendakis.overstayCharge', function ($q) {
                    $q->where('status', 'final')->whereDoesntHave('payment');
                });
            }
        }

        // 🔹 **Filter berdasarkan pencarian (Nama atau Email)**
        if ($request->filled('search')) {
            $queryUtama->where(function ($query) use ($request) {
                $query->whereHas('pendakis.biodata', function ($q) use ($request) {
                    $q->where('first_name', 'like', '%' . $request->search . '%')
                        ->orWhere('last_name', 'like', '%' . $request->search . '%');
                })->orWhereHas('user', fn ($q) => $q->where('email', 'like', '%' . $request->search . '%'));
            });
        }

        // 🔹 **Optimasi Urutan Data**
        $queryUtama->orderBy('gk_bookings.tanggal_masuk', 'desc');

        // Query Prioritas
        $dataPrioritas = (clone $queryUtama)->where('gk_bookings.status_booking', 3)
            ->whereHas('destinasi', function ($q) use ($destinasi) {
                $q->where('destinasis.id', $destinasi->id);
            })
            ->whereHas('pembayaran', function ($q3) {
                $q3->where('status', 'pending');
            })
            ->get();

        // Ambil data dengan paginasi
        $data = $queryUtama->paginate(20);


        return view('etiket.admin.destinasi.booking.index', [
            'destinasi' => $destinasi,
            'data' => $data,
            'dataPrioritas' => $dataPrioritas
        ]);
    }

    public function cancelBooking(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|uuid|exists:gk_bookings,id',
            'reason' => 'required|string|max:500',
        ]);
        app(\App\Services\BookingCancellationService::class)->cancel(
            gk_booking::findOrFail($request->booking_id), $request->user(), $request->reason
        );
        return back()->with('success', 'Pesanan dibatalkan. Riwayat dan bukti pembayaran tetap tersimpan.');
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

        return \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $booking = gk_booking::with('pembayaran')->lockForUpdate()->findOrFail($request->id_booking);
            $userBooking = User::with('biodata')->find($booking->id_user);
            if (!$booking) {
                abort(404);
            }

            if ((int) $booking->status_booking === gk_booking::STATUS_CANCELLED) {
                return redirect()->back()->withErrors('Pesanan dibatalkan dan tidak dapat dikonfirmasi.');
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
                        'validator' =>  $this->userAdmin()->id
                    ]);
                }
                $lastPembayaran = $booking->pembayaran->last();
                $lastPembayaran->update([
                    'status' => $request->verified === 'yes' ? 'success' : 'failed',
                    'keterangan' => $request->keterangan,
                    'validator' =>  $this->userAdmin()->id
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
                // return $dataStruk;

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
        });
    }

    public function showBooking($id)
    {
        $booking = gk_booking::with(['gateMasuk', 'gateKeluar', 'pendakis.biodata.user', 'pendakis.getStatus', 'cancelledBy', 'destinasi'])->where('id', $id)->first();


        $listStatusPendakian = $booking->riwayatPendakian();

        $overstay = $this->overstay->bookingSummary($booking);

        // return $listStatusPendakian;


        // return $listStatusPendakian[0];
        return view('etiket.admin.destinasi.booking.showBooking', [
            'booking' => $booking,
            'listStatusPendakian' => $listStatusPendakian,
            'overstay' => $overstay,
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
            ->whereBetween('status_booking', [4, 8])
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
            // Cek out hanya melalui checkoutAction agar waktu dan biaya selalu tercatat.
            'name' => 'required|integer|in:0,1,2,4',
            'pendakis' => 'nullable|array',
            'pendakis.*' => 'nullable|in:0,1'
        ]);

        // return $request;

        // cek status booking >= 4
        $booking = gk_booking::find($request->booking_id);
        if ($booking->status_booking < 4 || $booking->status_booking > 8) {
            return redirect()->back()->withErrors('Bookingan belum menyelesaikan pembayaran');
        }

        $message = [];

        // jika tanggal masuk < hari ini, cuma bisa memberi konfirmasi batal pendakian
        if ($booking->tanggal_masuk > Carbon::now(OverstayCalculator::TZ)->toDateString()) {
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
        $booking->load('pendakis.getStatus', 'pendakis.overstayCharge.payment');
        // Cek jika semua pendaki dalam booking memiliki status terakhir 3 (Cek Out) atau 1 (Batal Mendaki)
        $semuaSelesai = $booking->pendakis->every(function ($pendaki) {
            $lastStatus = $pendaki->getStatus->last();
            return $lastStatus && in_array($lastStatus->status, [1, 3]);
        });

        // Jika semua pendaki sudah selesai (Cek Out atau Batal), update status booking menjadi 7
        if ($semuaSelesai) {
            $adaTagihanBelumLunas = $booking->pendakis->contains(function ($pendaki) {
                return $pendaki->overstayCharge && !$pendaki->overstayCharge->payment;
            });

            if ($adaTagihanBelumLunas) {
                return ['error' => ['Masih ada biaya tambahan yang belum dibayar']];
            }

            $booking->update([
                'status_booking' => 8,
                'validator' =>  $this->userAdmin()->id
            ]);
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
                        'validator' =>  $this->userAdmin()->id,
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
        $booking->load('pendakis.getStatus', 'pendakis.biodata');
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
                        'validator' =>  $this->userAdmin()->id,
                    ]);
                    // Bekukan waktu masuk dan dasar tarif tambahan saat check-in.
                    $pendaki->update(['checkin_at' => Carbon::now(config('app.timezone'))]);
                    $this->freezeTarifSnapshot($pendaki);
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

    /**
     * Bekukan kategori + tarif masuk (weekend/weekday) pendaki saat check-in.
     */
    private function freezeTarifSnapshot($pendaki)
    {
        $kategori = ($pendaki->biodata->kenegaraan ?? 'ID') === 'ID' ? 'wni' : 'wna';
        $tiket = gk_tiket_pendaki::where('id_paket_tiket', $pendaki->booking->id_tiket)
            ->where('kategori_pendaki', $kategori)
            ->first();

        if (!$tiket) {
            return;
        }

        GkPendakiTarifSnapshot::updateOrCreate(
            ['id_pendaki' => $pendaki->id],
            [
                'kategori' => $kategori,
                'harga_masuk_wk' => $tiket->harga_masuk_wk,
                'harga_masuk_wd' => $tiket->harga_masuk_wd,
                'source' => 'snapshot',
            ]
        );
    }

    public function showStruk($id)
    {
        $booking = gk_booking::where('id', $id)->first();

        // if ($booking->status_pembayaran) {
        //     $booking = json_decode($booking->dataStruk);
        //     return "ok 1";
        // } else {
        //     $booking = $this->helper->getDataStruk($booking->id);
        //     return "ok ";
        // }

        $booking = json_decode($booking->dataStruk);
        if (!isset($booking)) {
            abort(404, 'Data tidak ditemukan');
        }

        return view('etiket.admin.destinasi.booking.showStruk', [
            'data' => $booking
        ]);
    }

    /**
     * Catat kepulangan aktual per pendaki (dengan waktu turun, opsional mundur + alasan).
     */
    public function checkoutAction(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|uuid|exists:gk_bookings,id',
            'checkout' => 'required|array|min:1',
            'checkout.*' => 'nullable|date',
            'reasons' => 'nullable|array',
            'reasons.*' => 'nullable|string|max:255',
        ]);

        $booking = gk_booking::find($request->booking_id);
        if (!$booking || $booking->status_booking < 4 || $booking->status_booking > 8) {
            return redirect()->back()->withErrors('Booking tidak valid');
        }

        $items = [];
        foreach ($request->input('checkout', []) as $idPendaki => $time) {
            $items[] = [
                'pendaki_id' => $idPendaki,
                'actual_checkout_at' => $time ? Carbon::parse($time, OverstayCalculator::TZ) : null,
                'reason' => $request->input("reasons.$idPendaki"),
            ];
        }

        try {
            $this->checkout->checkout($booking, $items, $this->userAdmin());
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }

        return redirect()->back()->with('success', 'Kepulangan pendaki berhasil dicatat');
    }

    /**
     * Catat pembayaran biaya tambahan di pos (nominal dari tagihan server).
     */
    public function recordPaymentAction(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|uuid|exists:gk_bookings,id',
            'charge_id' => 'required|uuid|exists:gk_overstay_charges,id',
            'method' => 'required|string|max:100',
            'reference' => 'nullable|string|max:100',
            'note' => 'nullable|string|max:255',
        ]);

        $booking = gk_booking::find($request->booking_id);
        if (!$booking) {
            return redirect()->back()->withErrors('Booking tidak ditemukan');
        }

        $charge = GkOverstayCharge::where('id', $request->charge_id)
            ->where('id_booking', $booking->id)
            ->first();

        if (!$charge) {
            return redirect()->back()->withErrors('Tagihan tidak ditemukan');
        }

        try {
            $this->paymentService->record(
                $booking,
                $charge,
                $request->method,
                $this->userAdmin(),
                $request->reference,
                $request->note
            );
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }

        return redirect()->back()->with('success', 'Pembayaran biaya tambahan berhasil dicatat');
    }
}
