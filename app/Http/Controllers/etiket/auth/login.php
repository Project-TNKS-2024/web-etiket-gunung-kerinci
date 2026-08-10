<?php

namespace App\Http\Controllers\etiket\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class login extends Controller
{
    public function login()
    {
        return view('etiket.auth.login'); // Ganti 'login' dengan nama view yang sesuai
    }

    public function actionlogin(Request $request)
    {
        // Validate input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            // 'g-recaptcha-response' => 'recaptcha',

        ]);

        // Get credentials
        $credentials = $request->only('email', 'password');

        // Attempt authentication
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            switch ($user->role) {
                case 'admin':
                    return redirect()->intended(route('admin.dashboard'))
                        ->with('success', 'Berhasil login sebagai admin.');
                case 'user':
                    return redirect()->intended(route('user.dashboard'))
                        ->with('success', 'Berhasil login sebagai pengguna.');
                default:
                    Auth::logout(); // logout jika role tidak dikenali
                    return redirect()->route('login')->withErrors([
                        'email' => 'Akun tidak memiliki akses yang valid.',
                    ]);
            }
        }

        // If authentication fails, return with error message
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    public function logout()
    {
        Auth::logout();

        return redirect(route('homepage.beranda'));
    }
}
