<?php

namespace App\Http\Controllers\etiket\admin\master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\helper\BookingHelperController;
use App\Http\Controllers\helper\uploadFileControlller;
use App\Http\Controllers\homepage\booking;
use App\Models\gk_booking;
use App\Models\pembayaran;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ValidasiPembayaran extends Controller
{
    private $helper;
    private $upload;

    public function __construct(BookingHelperController $helper, uploadFileControlller $upload)
    {
        $this->helper = $helper;
        $this->upload = $upload;
    }

    //
    public function index(Request $request)
    {
        $query = gk_booking::with('pembayaran', 'pendakis', 'pendakis.biodata', 'user');

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

        return view('etiket.admin.master.validasi.index', compact('dataBooking'));
    }

    public function updateAction(Request $request)
    {
        $request->validate([
            'id_booking' => 'required|string',
            'keterangan' => 'required|string|nullable|max:255',
            'verified' => 'required|in:yes,no',
        ]);

        $booking = gk_booking::with('pembayaran')->findOrFail($request->id_booking);
        if (!$booking) {
            abort(404);
        }

        // return $booking;
        $booking->update([
            'unique_code' => $request->verified === 'yes' ? $this->helper->generateCode(10) : null,
            'status_booking' => $request->verified === 'yes' ? 4 : 3,
            'status_pembayaran' => $request->verified === 'yes' ? 1 : 0,

        ]);


        if ($booking->pembayaran->isNotEmpty()) {
            foreach ($booking->pembayaran as $pembayaran) {
                $pembayaran->update([
                    'status' => $request->verified === 'yes' ? 'success' : 'failed',
                ]);
            }

            // Perbarui keterangan pembayaran terakhir
            $lastPembayaran = $booking->pembayaran->last();
            $lastPembayaran->update([
                'keterangan' => $request->keterangan,
            ]);
        } else {
            return redirect()->back()->with('error', 'Pembayaran tidak ditemukan');
        }



        return redirect()->route('admin.master.validasiPembayaran')->with('success', 'Pengajuan berhasil diperbarui');
    }
}
