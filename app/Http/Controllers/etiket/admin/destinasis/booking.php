<?php

namespace App\Http\Controllers\etiket\admin\destinasis;

use App\Http\Controllers\Controller;
use App\Models\gk_booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class booking extends Controller
{
    public function readNow()
    {
        $today = Carbon::today();
        $oneMonthBefore = $today->copy()->subMonth();
        $oneMonthAfter = $today->copy()->addMonth();

        $bookings = gk_booking::with(['user', 'gktiket.destinasi', 'gateMasuk', 'gateKeluar', 'pendakis'])
            ->whereBetween('tanggal_masuk', [$oneMonthBefore, $oneMonthAfter])
            ->get();
        // $bookings = gk_booking::with(['user', 'gktiket.destinasi', 'gateMasuk', 'gateKeluar', 'pendakis'])->get();
        // return $bookings;
        return view('etiket.admin.gunung.booking.read-now', compact('bookings'));
    }
}
