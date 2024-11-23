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

        $telp_country = explode(" ", $user->no_hp);
        // Validasi format nomor telepon
        if (count($telp_country) === 2) {
            $user->no_hp = $telp_country[1];
            $user->telp_country = $telp_country[0];
        } else {
            $user->telp_country = '';
        }

        // return $user;

        return view('etiket.user.sections.profile', [
            'user' => $user,
        ]);
    }

    public function action(Request $request)
    {
        // return $request;
        $user  = Auth::user();
        // return $request;
        $request->validate([
            'firstName' => 'required',
            'lastName' => 'required',
            'kewarganegaraan' => 'required|in:wni,wna',
            'nik' => 'required',
            'telp_country' => 'required',
            'nomor_telepon' => 'required|numeric',
        ]);

        if ($request->nomor_telepon[0] == 0) {
            $request->nomor_telepon = substr($request->nomor_telepon, 1);
        }
        $request->nomor_telepon = $request->telp_country . ' ' . $request->nomor_telepon;


        User::where('id', $user->id)->update([
            "nik" => $request->nik,
            "no_hp" => $request->nomor_telepon,
            "kewarganegaraan" => $request->kewarganegaraan,
            "firstName" => $request->firstName,
            "lastName" => $request->lastName
        ]);

        return back()->with('success', 'Berhasil mengubah data');
    }
}
