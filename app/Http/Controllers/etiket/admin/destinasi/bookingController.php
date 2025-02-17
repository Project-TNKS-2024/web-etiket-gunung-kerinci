<?php

namespace App\Http\Controllers\etiket\admin\destinasi;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\helper\BookingHelperController;
use App\Models\gk_booking as ModelBooking;
use App\Models\gk_booking;
use Illuminate\Http\Request;

class bookingController extends AdminController
{
    private $helper;

    public function __construct(BookingHelperController $helper)
    {
        $this->helper = $helper;
    }

    public function index($id, Request $request)
    {
        $filter = $request->filter_ipt;
        $search = $request->search_ipt;

        // Menggunakan query builder untuk memulai query
        // $query = ModelBooking::with('pendakis', 'gateMasuk', 'gateKeluar', 'gkTiket')->where('id_destinasi', $id);
        $query = ModelBooking::with('pendakis', 'gateMasuk', 'gateKeluar', 'gkTiket', 'pendakis.biodata')
            ->whereHas('gkTiket', function ($q) use ($id) {
                $q->where('id_destinasi', $id);
            });


        // Jika ada pencarian, terapkan pencarian
        if ($search) {
            $query->where(function ($q) use ($search) {
                // Pencarian di kolom ModelBooking
                $q->where('status_pembayaran', 'like', '%' . $search . '%')
                    ->orWhere('status_booking', 'like', '%' . $search . '%')
                    ->orWhere('tanggal_masuk', 'like', '%' . $search . '%');

                // Pencarian di relasi 'pendakis'
                $q->orWhereHas('pendakis', function ($query) use ($search) {
                    $query->where('nama', 'like', '%' . $search . '%');
                });

                // Pencarian di relasi 'gateMasuk'
                $q->orWhereHas('gateMasuk', function ($query) use ($search) {
                    $query->where('nama', 'like', '%' . $search . '%');
                });

                // Pencarian di relasi 'gateKeluar'
                $q->orWhereHas('gateKeluar', function ($query) use ($search) {
                    $query->where('nama', 'like', '%' . $search . '%');
                });
            });
        }
        // Jika filter berubah, reset ke halaman 1
        if ($filter) {
            $filter = $request->filter_ipt; // Ambil nilai filter

            if ($filter == 1) {
                $query->where('status_pembayaran', '0');
            } elseif ($filter == 2) {
                $query->where('status_pembayaran', '1');
            } elseif ($filter == 3) {
                $query->where('status_booking', '6');
            }
        }


        // Ambil data dengan paginasi
        $data = $query->paginate(10);


        return view('etiket.admin.destinasi.booking.index', [
            'id' => $id,
            'filter' => $filter,
            'search' => $search,
            'data' => $data
        ]);
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
