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
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'phone' => 'required|string|max:20', // Sesuaikan dengan kebutuhan Anda
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'firstName.required' => 'Nama lengkap harus diisi.',
            'firstName.max' => 'Nama lengkap maksimal :max karakter.',
            'lastName.required' => 'Nama lengkap harus diisi.',
            'lastName.max' => 'Nama lengkap maksimal :max karakter.',
            'phone.required' => 'Nomor handphone harus diisi.',
            'phone.max' => 'Nomor handphone maksimal :max karakter.',
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
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'no_hp' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Redirect ke halaman login setelah registrasi berhasil
        return redirect()->route('login')->with(
            'success',
            'Registrasi berhasil. Silakan login untuk melanjutkan.'
        );
    }
}
