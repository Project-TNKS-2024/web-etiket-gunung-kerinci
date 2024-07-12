<?php

namespace App\Http\Controllers\etiket\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GantiPassword extends Controller
{
    public function index()
    {
        return view('etiket.user.sections.ganti-password');
    }

    public function resetAction(Request $request)
    {
        // For demonstration purposes
        return back()->withErrors([
            'password' => 'Password tidak sesuai',
        ]);

        // Actual logic
        $credentials = $request->only('password', 'konfirmasiPassword');

        if (Auth::attempt($credentials)) {
            // If authentication is successful
            return redirect()->intended('/'); // Replace '/' with the route you want to redirect to after login
        }

        // If authentication fails
        return back()->withErrors([
            'password' => 'The provided credentials do not match our records.',
        ]);
    }
}

