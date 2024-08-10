<?php

namespace App\Http\Controllers\etiket\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\gk_booking;
use Illuminate\Support\Facades\Auth;

class riwayat extends Controller
{
    public function index()
    {

        if (!Auth::check()) {
            abort(404);
        }
        $booking = gk_booking::with(['gateMasuk', 'gateKeluar', 'pendakis'])->where('id_user', Auth::user()->id)->get();
        // $booking = gk_booking::with(['gateMasuk', 'gateKeluar'])->where('id_user', Auth::user()->id)->get();
        return view('etiket.user.sections.riwayat', [
            'bookings' => $booking
        ]);
    }
}
