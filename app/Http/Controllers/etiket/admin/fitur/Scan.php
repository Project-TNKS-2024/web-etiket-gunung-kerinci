<?php

namespace App\Http\Controllers\etiket\admin\fitur;

use App\Http\Controllers\AdminController;
use App\Models\gk_booking;
use Illuminate\Http\Request;

class Scan extends AdminController
{
    public function index()
    {
        return view('etiket.admin.fitur.scan', [
            'page' => 'Scan',
        ]);
    }
    public function scanTiketAction($code)
    {
        $booking = gk_booking::where('unique_code', $code)->with('gateMasuk', 'gateKeluar', 'destinasi', 'pendakis')->first();
        if ($booking) {
            return redirect()->route('admin.destinasi.booking.show', ['id' => $booking->id]);
        } else {
            return redirect()->back()->withErrors('kode tidak ditemukan');
        }
    }
}
