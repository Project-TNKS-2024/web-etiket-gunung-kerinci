<?php

namespace App\Http\Controllers\etiket\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\gk_booking;


class dashboard extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $booking = gk_booking::with(['gateMasuk', 'gateKeluar'])->where('id_user', Auth::user()->id)->get();
        return view('etiket.user.sections.dashboard', [
            'user' => $user,
            'booking' => $booking,
        ]);
    }

    public function action(Request $request, $id) {
        $request->validate([
            // 'kewarganegaraan' => 'required',
            'nama_lengkap' => 'required',
            // 'jenis_identitas' => 'required',
            'id_pendaftar' => 'required',
            'nomor_telepon' => 'required',
            // 'nomor_telepon_darurat' => 'required',
            // 'tgl_lahir' => 'required',
            // 'usia' => 'required',
        ]);

        User::where('id', $id)->update([
            "nik" => $request->id_pendaftar,
            "no_hp" => $request->nomor_telepon,
            "fullname" => $request->nama_lengkap,
        ]);

        return back()->with('success', 'Berhasil mengubah data');
    }

}
