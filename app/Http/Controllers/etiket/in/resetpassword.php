<?php

namespace App\Http\Controllers\etiket\in;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class resetpassword extends Controller
{
    public function resetpassword()
    {
        return view('etiket.in.reset-password'); // Ganti 'register' dengan nama view yang sesuai
    }

    public function actionlupapassword(Request $request)
    {
        return back()->withErrors([
            'password' => 'Password tidak sesuai',
        ]);
    }
}
