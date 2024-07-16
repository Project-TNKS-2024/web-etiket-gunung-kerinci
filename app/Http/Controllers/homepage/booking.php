<?php

namespace App\Http\Controllers\homepage;

use App\Http\Controllers\Controller;
use App\Models\gk_booking;
use Illuminate\Http\Request;
use App\Models\gk_gates;
use App\Models\tiket;
use Illuminate\Support\Facades\Auth;

class booking extends Controller
{
    public function index(Request $request)
    {
        $gates = gk_gates::all();
        $tiket = tiket::where('spesial', 'gunungKerinci')->get();
        return view("homepage.booking.booking", ["gates" => $gates, "tikets" => $tiket]);
    }

    public function getbooking(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('etiket.in.login');
        }

        if (Auth::user()->role == 'user') {
            $latestBooking = gk_booking::where('id_user', Auth::id())
                ->where('status', 0)
                ->latest()
                ->first();
            if ($latestBooking) {
                $latestBooking->tanggal_masuk = $request->input('date-start');
                $latestBooking->tanggal_keluar = $request->input('date-end');
                $latestBooking->wni = $request->input('wni');
                $latestBooking->wna = $request->input('wna');
                $latestBooking->gate_masuk = $request->input('gerbang-masuk');
                $latestBooking->gate_keluar = $request->input('gerbang-keluar');

                $latestBooking->save();
            } else {
                gk_booking::create([
                    'id_user' =>$request,
                    'id_tiket',
                    'status',
                    'id_booking_master',
                    'total_pendaki',
                    'wni',
                    'wna',
                    'keterangan',
                    'QR',
                    'pembayaran',
                    'gate_masuk',
                    'gate_keluar',
                    'tanggal_masuk',
                    'tanggal_keluar',
                ]);
            }
        } else {
            // jika yang login adalah admin 
            // arahkan ke fitur kelola booking gunung kerinci
        }

        return response()->json($request);
    }
    public function bookingStep1($id)
    {
        return $id;
    }
}
