<?php

namespace App\Http\Controllers\etiket\admin\destinasi;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\helper\BookingHelperController;
use App\Mail\BookingPayment;
use App\Models\destinasi;
use App\Models\gk_booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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

        if ($request->verified === 'yes') {
            $booking->update([
                'unique_code' =>  $this->helper->generateCode(10),
                'status_booking' =>  4,
                'status_pembayaran' =>  1,
            ]);
            $struk = $this->helper->getDataStruk($booking->id);
            $booking->update([
                'dataStruk' => $struk,
            ]);

            $order = [
                'name' => $userBooking->biodata->first_name . ' ' . $userBooking->biodata->last_name,
                'booking_code' => $booking->unique_code,
                'amount' => $booking->total_pembayaran,
                'payment_date' => $booking->pembayaran->last()->created_at,
                'invoice_url' => route('homepage.booking.struk', $booking->id),
                'email' => $userBooking->email,
            ];
            Mail::to($userBooking->email)->send(new BookingPayment($order));
        } else {
            if ($booking->status_booking > 4) {
                return redirect()->back()->withErrors('Bookingan sudah melakukan pendakian');
            }
            $booking->update([
                'unique_code' => null,
                'status_booking' =>  3,
                'status_pembayaran' =>  0,
                'dataStruk' => null,
            ]);
        }


        if ($booking->pembayaran->isNotEmpty()) {
            // Perbarui keterangan pembayaran terakhir
            $lastPembayaran = $booking->pembayaran->last();
            $lastPembayaran->update([
                'status' => $request->verified === 'yes' ? 'success' : 'failed',
                'keterangan' => $request->keterangan,
            ]);
        } else {
            return redirect()->back()->with('error', 'Pembayaran tidak ditemukan');
        }

        return redirect()->back()->with('success', 'Pengajuan berhasil diperbarui');
    }

    public function showBooking($id)
    {
        // $booking = gk_booking::with('pendakis', 'pendakis.biodata', 'gateMasuk', 'gateKeluar', 'gkTiket', 'pendakis')->find($id_booking);
        $booking = gk_booking::with(['gateMasuk', 'gateKeluar', 'pendakis.biodata', 'destinasi'])->where('id', $id)->first();

        // return $booking->pendakis[0];
        return view('etiket.admin.destinasi.booking.showBooking', [
            'booking' => $booking
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
        $booking = gk_booking::find($request['id']);
        if ($booking->status_booking < $request['status']) {
            $booking->update([
                'status_booking' => $request['status']
            ]);
        }

        return redirect()->back()->with('success', 'Status booking berhasil diperbarui');
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
