<?php

namespace App\Http\Controllers\etiket\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use PharIo\Manifest\Email;
use Illuminate\Support\Facades\Hash;

class register extends Controller
{
    public function register()
    {
        return view('etiket.auth.register'); // Ganti 'register' dengan nama view yang sesuai
    }

    public function actionregister(Request $request)
    {
        // Validasi data yang diterima dari form registrasi
        $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'email.max' => 'Email maksimal :max karakter.',
            'password.required' => 'Password harus diisi.',
            'password.min' => 'Password minimal :min karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);
        // Membuat user baru berdasarkan data yang diterima
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // bikin kan verifikasi email
        // $user->sendEmailVerificationNotification();

        // Redirect ke halaman login setelah registrasi berhasil
        return redirect()->route('login')->with(
            'success',
            'Registrasi berhasil. Silakan login untuk melanjutkan.'
        );
    }
}
