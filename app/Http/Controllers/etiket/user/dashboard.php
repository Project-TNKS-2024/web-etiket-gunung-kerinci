<?php

namespace App\Http\Controllers\etiket\user;

use App\Http\Controllers\Controller;
use App\Models\gk_booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class dashboard extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $booking = gk_booking::where('id_user', $user->id)->get();
        // return $booking;
        return view('etiket.user.sections.dashboard', [
            'user' => $user,
            'bookings' => $booking,
        ]);
    }
}
