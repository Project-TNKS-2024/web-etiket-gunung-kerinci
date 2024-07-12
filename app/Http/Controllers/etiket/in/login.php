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
        // Validate input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',  // Ensure password is required
        ]);

        // Get credentials
        $credentials = $request->only('email', 'password');

        // Attempt authentication
        if (Auth::attempt($credentials)) {
            if (Auth::user()->role == 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            } else if (Auth::user()->role == 'user') {
                return redirect()->intended(route('user.dashboard'));
            }
        }

        // If authentication fails, return with error message
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout()
    {
        Auth::logout();

        return redirect(route('homepage.beranda'));
    }
}
