<?php

namespace App\Http\Controllers\etiket\admin\fitur;

use App\Http\Controllers\Controller;
use App\Models\gk_booking;
use Illuminate\Http\Request;

class Scan extends Controller
{
    public function index()
    {
        return view('etiket.admin.fitur.scan', [
            'page' => 'Scan',
        ]);
    }
    public function detailtiket($uq)
    {
        $booking = gk_booking::with(['gateMasuk', 'gateKeluar', 'pendakis'])->where('unique_code', $uq)->first();
        // return $booking;
        return view('etiket.admin.fitur.detailtiket', [
            'page' => 'Detail Tiket',
            'booking' => $booking,
        ]);
    }
}
