<?php

namespace App\Http\Controllers\etiket\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\gk_booking;

use function PHPUnit\Framework\isEmpty;

class dashboard extends Controller
{
    public function index()
    {
        $user = User::with('biodata')->find(Auth::user()->id);
        // return $user;
        $booking = gk_booking::where('id_user', $user->id)->with('pendakis')->get();
        return view('etiket.user.sections.dashboard', [
            'user' => $user,
            'bookings' => $booking,
        ]);
    }

    public function riwayat()
    {
        $user = User::with('biodata')->find(Auth::user()->id);
        // return $user;
        $booking = gk_booking::where('id_user', $user->id)->get();
        return view('etiket.user.sections.riwayat', [
            'bookings' => $booking,
        ]);
    }
}
