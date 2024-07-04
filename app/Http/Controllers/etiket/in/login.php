<?php

namespace App\Http\Controllers\etiket\in;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class login extends Controller
{
    public function login()
    {
        return view('etiket.in.login'); // Ganti 'login' dengan nama view yang sesuai
    }

    public function actionlogin(Request $request)
    {
        // untuk sementara
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);

        // asli

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Jika autentikasi berhasil
            return redirect()->intended('/'); // Ganti '/' dengan route yang ingin dituju setelah login
        }

        // Jika autentikasi gagal
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout()
    {
        Auth::logout();

        return redirect('dashboard'); // Ganti '/login' dengan route yang ingin dituju setelah logout
    }
}
