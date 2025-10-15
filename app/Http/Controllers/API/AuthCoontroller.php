<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\helper\ApiResponse;
use App\Mail\MobileVerifyMail;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Exception;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;

class AuthCoontroller extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Validasi gagal', $validator->errors(), 422);
        }

        if (Auth::attempt($request->only('email', 'password'))) {
            $user  = Auth::user();
            $token = $user->createToken('api-token')->plainTextToken;

            return ApiResponse::success([
                'user'  => $user,
                'token' => $token
            ], 'Login berhasil');
        }

        return ApiResponse::error('Email atau password salah', null, 401);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return ApiResponse::success(null, 'Logout berhasil');
    }

    public function register(Request $request)
    {
        // 1️⃣ VALIDASI INPUT
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Validasi gagal', $validator->errors(), 422);
        }

        // 2️⃣ CEK MANUAL JIKA EMAIL SUDAH ADA (redundan tapi aman)
        if (User::where('email', $request->email)->exists()) {
            return ApiResponse::error('Akun dengan email ini sudah terdaftar.', [], 409);
        }

        // 3️⃣ BUAT USER BARU
        $user = User::create([
            'email'      => $request->email,
            'gauth_type' => 'manual',
            'password'   => Hash::make($request->password),
        ]);


        // 4️⃣ KIRIM EMAIL VERIFIKASI
        try {
            Mail::to($user->email)->send(new MobileVerifyMail($user));
        } catch (Exception $mailError) {
            // Jika gagal kirim email, hapus user agar tidak ada data "gantung"
            // $user->delete();
            return ApiResponse::error(
                'Gagal mengirim email verifikasi. Silakan coba lagi nanti.',
                ['mail_error' => $mailError->getMessage()],
                500
            );
        }

        // 5️⃣ BUAT TOKEN AUTENTIKASI
        $token = $user->createToken('api-token')->plainTextToken;

        // 6️⃣ RESPONSE BERHASIL
        return ApiResponse::success([
            'user'  => $user,
            'token' => $token,
        ], 'Registrasi berhasil. Silakan cek email untuk verifikasi.', 201);
    }
    public function notice(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return ApiResponse::success(null, 'Email sudah diverifikasi.');
        }

        return ApiResponse::error('Email belum diverifikasi', null, 403);
    }

    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return ApiResponse::success(null, 'Email sudah diverifikasi.');
        }

        // kirim ulang email verifikasi ke mobile
        Mail::to($request->user()->email)->send(new MobileVerifyMail($request->user()));

        return ApiResponse::success(null, 'Email verifikasi telah dikirim ulang.');
    }

    public function verify(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return ApiResponse::success(null, 'Email sudah diverifikasi.');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return ApiResponse::success(null, 'Email berhasil diverifikasi.');
    }


    public function sendResetLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Validasi gagal', $validator->errors(), 422);
        }

        $token = bin2hex(random_bytes(30));

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token]
        );

        Mail::to($request->email)->send(new ResetPasswordMail($token));

        return ApiResponse::success(null, 'Link reset password sudah dikirim ke email');
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token'    => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')->where('token', $request->token)->first();

        if (!$record) {
            return ApiResponse::error('Token tidak valid', null, 404);
        }

        $user = User::where('email', $record->email)->firstOrFail();
        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('token', $request->token)->delete();

        return ApiResponse::success(null, 'Password berhasil direset, silakan login.');
    }

    public function redirectToGoogle()
    {
        return ApiResponse::success([
            'redirect_url' => Socialite::driver('google')->stateless()->redirect()->getTargetUrl()
        ], 'Redirect ke Google');
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::firstOrCreate(
                ['gauth_id' => $googleUser->id],
                [
                    'name'              => $googleUser->name,
                    'email'             => $googleUser->email,
                    'gauth_type'        => 'google',
                    'email_verified_at' => now(),
                    'password'          => bcrypt(Str::random(16)),
                ]
            );

            $token = $user->createToken('api-token')->plainTextToken;

            return ApiResponse::success([
                'user'  => $user,
                'token' => $token
            ], 'Login dengan Google berhasil');
        } catch (Exception $e) {
            return ApiResponse::error('Login dengan Google gagal', $e->getMessage(), 400);
        }
    }
}
