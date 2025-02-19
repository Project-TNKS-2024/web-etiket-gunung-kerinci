<?php

namespace App\Http\Controllers\etiket\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'gauth_type' => 'manual',
            'password' => Hash::make($request->password),
        ]);

        // Kirim email verifikasi
        event(new Registered($user));

        // Redirect dengan pesan sukses
        return redirect()->route('login')->with(
            'success',
            'Registrasi berhasil. Silakan cek email Anda untuk verifikasi sebelum login.'
        );
    }
    public function noticeEmail()
    {
        if (Auth::user()->hasVerifiedEmail()) {
            return redirect()->intended();
        }

        return view('etiket.auth.verify-email');
    }

    public function resendEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('homepage.beranda');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Email verifikasi telah dikirim ulang.');
    }

    public function verifyEmail(EmailVerificationRequest $request, $id, $hash)
    {
        $request->fulfill();

        return redirect('/login')->with('success', 'Email Anda telah diverifikasi.Silahkan login.');
    }
}
