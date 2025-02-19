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
    public function __construct()
    {
        // cek apakah user.gauth_type = manual
        if (Auth::user()->gauth_type !== 'manual') {
            return redirect()->route('user.dashboard')->with('error', 'Anda tidak memiliki akses untuk mengakses halaman ini.');
        }
    }
    public function resetAction(Request $request)
    {
        // Actual logic
        $request->validate([
            'password_baru' => 'required|string|min:8|confirmed',
        ], [
            'password_baru.required' => 'Password harus diisi.',
            'password_baru.min' => 'Password minimal :min karakter.',
            'password_baru.confirmed' => 'Konfirmasi password tidak cocok.',
            'password_baru.required' => 'Password harus diisi.',
            'password_baru.min' => 'Password minimal :min karakter.',
            'password_baru.confirmed' => 'Periksa kembali password.',
        ]);

        $user = User::where('email', Auth::user()->email)->first();

        // Update the user's password
        $user->password = Hash::make($request->password);

        if ($user->save())
            return back()->with('success', 'Password Anda telah berhasil direset.');
    }
}
