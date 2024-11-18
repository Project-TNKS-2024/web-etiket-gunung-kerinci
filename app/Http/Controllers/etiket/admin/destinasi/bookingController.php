<?php

namespace App\Http\Controllers\etiket\admin\destinasi;

use App\Http\Controllers\Controller;
use App\Models\gk_booking as ModelBooking;
use Illuminate\Http\Request;

class bookingController extends Controller
{
    public function index($id, Request $request)
    {
        $filter = $request->filter_ipt;
        $search = $request->search_ipt;

        // Menggunakan query builder untuk memulai query
        $query = ModelBooking::with('pendakis', 'gateMasuk', 'gateKeluar');

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

        // return $data[1];

        return view('etiket.admin.destinasi.booking.index', [
            'id' => $id,
            'filter' => $filter,
            'search' => $search,
            'data' => $data
        ]);
    }
}
