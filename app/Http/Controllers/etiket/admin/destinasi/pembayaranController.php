<?php

namespace App\Http\Controllers\etiket\admin\destinasi;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\helper\BookingHelperController;
use App\Http\Controllers\helper\uploadFileControlller;
use App\Mail\BookingPayment;
use App\Models\destinasi;
use App\Models\gk_booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class pembayaranController extends AdminController
{
    private $helper;
    private $upload;

    public function __construct(BookingHelperController $helper, uploadFileControlller $upload)
    {
        $this->helper = $helper;
        $this->upload = $upload;
    }

    //
    public function index(Request $request, $id)
    {
        $destinasi = destinasi::find($id);

        $query = gk_booking::with('pembayaran', 'pendakis', 'pendakis.biodata', 'user', 'destinasi')
            ->whereHas('destinasi', function ($q) use ($id) {
                $q->where('destinasis.id', $id);
            });
        // filter booking bedasarkan id_destinasi

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

        // Filter untuk status_booking >= 3
        $query->where('status_booking', '>=', 3);


        // Tambahkan pengurutan berdasarkan created_at dari relasi pembayaran
        $query->orderByDesc(function ($subQuery) {
            $subQuery->select('created_at')
                ->from('pembayarans')
                ->whereColumn('pembayarans.id_booking', 'gk_bookings.id')
                ->latest()
                ->take(1);
        });

        $dataBooking = $query->paginate(50);

        // return $dataBooking;

        return view('etiket.admin.destinasi.pembayaran.index', [
            'dataBooking' => $dataBooking,
            'destinasi' => $destinasi,
        ]);
    }

    public function updateAction(Request $request)
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
}
