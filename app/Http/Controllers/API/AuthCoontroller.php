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
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthCoontroller extends Controller
{
    /**
     * Login dengan email dan password
     * Memvalidasi kredensial user dan menghasilkan token autentikasi
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Logout dengan menghapus token autentikasi user saat ini
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return ApiResponse::success(null, 'Logout berhasil');
    }

    /**
     * Registrasi user baru dengan email dan password
     * Membuat user baru, mengirim email verifikasi, dan menghasilkan token autentikasi
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        // VALIDASI INPUT
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Validasi gagal', $validator->errors(), 422);
        }

        // CEK MANUAL JIKA EMAIL SUDAH ADA (redundan tapi aman)
        if (User::where('email', $request->email)->exists()) {
            return ApiResponse::error('Akun dengan email ini sudah terdaftar.', [], 409);
        }

        // BUAT USER BARU
        $user = User::create([
            'email'      => $request->email,
            'gauth_type' => 'manual',
            'password'   => Hash::make($request->password),
        ]);


        // KIRIM EMAIL VERIFIKASI
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

        // BUAT TOKEN AUTENTIKASI
        $token = $user->createToken('api-token')->plainTextToken;

        // RESPONSE BERHASIL
        return ApiResponse::success([
            'user'  => $user,
            'token' => $token,
        ], 'Registrasi berhasil. Silakan cek email untuk verifikasi.', 201);
    }

    /**
     * Memeriksa status verifikasi email user
     * Mengembalikan status apakah email sudah diverifikasi atau belum
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function notice(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return ApiResponse::success(null, 'Email sudah diverifikasi.');
        }

        return ApiResponse::error('Email belum diverifikasi', null, 403);
    }

    /**
     * Mengirim ulang email verifikasi ke user
     * Hanya bisa dijalankan jika email belum diverifikasi
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return ApiResponse::success(null, 'Email sudah diverifikasi.');
        }

        // kirim ulang email verifikasi ke mobile
        Mail::to($request->user()->email)->send(new MobileVerifyMail($request->user()));

        return ApiResponse::success(null, 'Email verifikasi telah dikirim ulang.');
    }

    /**
     * Memverifikasi email user melalui link verifikasi
     * Menandai email user sebagai terverifikasi jika belum
     *
     * @param EmailVerificationRequest $request
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Mengirim link reset password ke email user
     * Membuat token reset password dan mengirimnya via email
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Reset password user menggunakan token
     * Memvalidasi token dan password baru, lalu memperbarui password di database
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Redirect user ke halaman login Google
     * Mengembalikan URL untuk autentikasi dengan Google OAuth
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToGoogle()
    {
        return ApiResponse::success([
            'redirect_url' => Socialite::driver('google')->stateless()->redirect()->getTargetUrl()
        ], 'Redirect ke Google');
    }

    /**
     * Menangani callback dari Google setelah user berhasil autentikasi
     * Membuat atau memperbarui user di database dan menghasilkan token autentikasi
     *
     * @return \Illuminate\Http\Response
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            $user = $this->findOrCreateGoogleUser($googleUser);

            $token = $user->createToken('api-token')->plainTextToken;

            return ApiResponse::success([
                'user'  => $user,
                'token' => $token
            ], 'Login dengan Google berhasil');
        } catch (Exception $e) {
            return ApiResponse::error('Login dengan Google gagal', $e->getMessage(), 400);
        }
    }

    /**
     * Login Google untuk mobile/API.
     *
     * Mobile app melakukan Google Sign-In di sisi aplikasi, lalu kirim Google
     * access token ke endpoint ini. Server akan validasi token ke Google lewat
     * Socialite dan mengembalikan Sanctum bearer token untuk akses API aplikasi.
     */
    public function loginWithGoogleToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'access_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Validasi gagal', $validator->errors(), 422);
        }

        try {
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->userFromToken($request->access_token);

            $user = $this->findOrCreateGoogleUser($googleUser);
            $token = $user->createToken('api-token')->plainTextToken;

            return ApiResponse::success([
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ], 'Login dengan Google berhasil');
        } catch (Exception $e) {
            return ApiResponse::error('Login dengan Google gagal', $e->getMessage(), 401);
        }
    }

    private function findOrCreateGoogleUser($googleUser): User
    {
        if (!$googleUser->email) {
            throw new Exception('Email Google tidak tersedia. Pastikan scope Google mencakup email.');
        }

        $user = User::where('gauth_id', $googleUser->id)
            ->orWhere('email', $googleUser->email)
            ->first();

        if (!$user) {
            $user = User::create([
                'email' => $googleUser->email,
                'gauth_id' => $googleUser->id,
                'gauth_type' => 'google',
                'email_verified_at' => now(),
                'password' => Hash::make(Str::random(32)),
                'avatar' => $googleUser->avatar,
            ]);
        } else {
            $user->forceFill([
                'gauth_id' => $googleUser->id,
                'gauth_type' => 'google',
                'email_verified_at' => $user->email_verified_at ?: now(),
                'avatar' => $googleUser->avatar ?: $user->avatar,
            ])->save();
        }

        return $user->fresh();
    }
}
