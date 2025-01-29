<?php

namespace App\Http\Controllers\etiket\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\gk_booking;
use App\Models\gk_pendaki;

use function PHPUnit\Framework\isEmpty;

class dashboard extends Controller
{
    public function index()
    {
        $user = User::with('biodata')->find(Auth::user()->id);

        $booking = gk_booking::whereHas('pendakis', function ($query) use ($user) {
            $query->where('id_bio', $user->id_bio); // Pastikan relasi 'biodata' ada
        })->with('pendakis')->get();

        return view('etiket.user.sections.dashboard', [
            'user' => $user,
            'bookings' => $booking,
        ]);
    }

    public function riwayat()
    {
        $user = User::with('biodata')->find(Auth::user()->id);
        // return $user;
        $booking = gk_booking::whereHas('pendakis', function ($query) use ($user) {
            $query->where('id_bio', $user->id_bio); // Pastikan relasi 'biodata' ada
        })->with('pendakis')->get();

        return view('etiket.user.sections.riwayat', [
            'bookings' => $booking,
        ]);
    }
}
