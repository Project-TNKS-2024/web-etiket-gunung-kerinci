<?php

namespace App\Http\Controllers\etiket\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class profile extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $user->nama_depan = explode(' ', $user->fullname)[0]; // Ambil kata pertama sebagai nama depan
        $user->nama_belakang = implode(' ', array_slice(explode(' ', $user->fullname), 1)); // Gabungkan sisanya sebagai nama belakang
        // return $user;
        return view('etiket.user.sections.profile', [
            'user' => $user,
        ]);
    }

    public function action(Request $request)
    {
        $user  = Auth::user();
        // return $request;
        $request->validate([
            'nama_depan' => 'required',
            'nama_belakang' => 'required',
            // 'kewarganegaraan' => 'required',
            'nik' => 'required',
            'nomor_telepon' => 'required',
        ]);

        User::where('id', $user->id)->update([
            "nik" => $request->nik,
            "no_hp" => $request->nomor_telepon,
            "fullname" => $request->nama_depan . ' ' . $request->nama_belakang,
        ]);

        return back()->with('success', 'Berhasil mengubah data');
    }
}
