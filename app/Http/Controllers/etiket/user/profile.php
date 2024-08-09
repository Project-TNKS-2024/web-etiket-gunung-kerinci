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
        return view('etiket.user.sections.profile', [
            'user' => $user,
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
