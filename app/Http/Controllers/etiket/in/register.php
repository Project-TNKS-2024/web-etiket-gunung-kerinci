<?php

namespace App\Http\Controllers\etiket\in;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use PharIo\Manifest\Email;
use Illuminate\Support\Facades\Hash;

class register extends Controller
{
    public function register()
    {
        return view('etiket.in.register'); // Ganti 'register' dengan nama view yang sesuai
    }

    public function actionregister(Request $request)
    {
        // Validasi data yang diterima dari form registrasi
        $request->validate([
            'fullname' => 'required|string|max:255',
            'phone' => 'required|string|max:20', // Sesuaikan dengan kebutuhan Anda
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'fullname.required' => 'Nama lengkap harus diisi.',
            'fullname.max' => 'Nama lengkap maksimal :max karakter.',
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
            'fullname' => $request->fullname,
            'no_hp' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Redirect ke halaman login setelah registrasi berhasil
        return redirect()->route('etiket.in.login')->with(
            'success',
            'Registrasi berhasil. Silakan login untuk melanjutkan.'
        );
    }

    // bagian lupa password
    // page masukkan email
    // page menunggu cek Email
    // page buat kata sandi baru

}
