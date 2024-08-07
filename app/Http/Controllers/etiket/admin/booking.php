<?php

namespace App\Http\Controllers\etiket\admin;

use App\Http\Controllers\Controller;
use App\Models\gk_booking;
use Illuminate\Http\Request;

class booking extends Controller
{
    public function readNow()
    {
        $bookings = gk_booking::with(['user', 'gktiket.destinasi', 'gateMasuk', 'gateKeluar', 'pendakis'])->get();
        // return $bookings;
        return view('etiket.admin.gunung.booking.read-now', compact('bookings'));
    }
}
