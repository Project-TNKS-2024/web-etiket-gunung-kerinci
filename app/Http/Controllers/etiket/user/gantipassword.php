<?php

namespace App\Http\Controllers\etiket\user;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class GantiPassword extends Controller
{
    public function index()
    {
        return view('etiket.user.sections.ganti-password');
    }

    public function resetAction(Request $request)
    {
        // Actual logic
        $email = Auth::user()->email;
        $request->validate([
            'password_baru' => 'required|string|min:8|confirmed',
        ], [
            'password_baru.required' => 'Password harus diisi.',
            'password_baru.min' => 'Password minimal :min karakter.',
            'password_baru.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        return Auth::user()->email;
        // Find the user by email

        $user = User::where(
            'email',
            $email
        )->first();

        if (!$user) {
            return back()->with(['error' => 'Email tidak ditemukan.']);
        }

        // Update the user's password
        $user->password = Hash::make($request->password);
        $user->save();
        // If authentication fails
        // Redirect to login page with success message
        return redirect()->route('login')->with('success', 'Password Anda telah berhasil direset.');
    }
}
