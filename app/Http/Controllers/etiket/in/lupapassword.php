<?php

namespace App\Http\Controllers\etiket\in;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class lupapassword extends Controller
{
    public function lupapassword()
    {
        return view('etiket.in.lupa-password'); // Ganti 'register' dengan nama view yang sesuai
    }

    public function actionlupapassword(Request $request)
    {
        return back()->with('success', 'Silakan cek email untuk mereset kata sandi');
    }
}
