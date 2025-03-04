<?php

namespace App\Http\Controllers\etiket\in;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\MyTestEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

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
    public function lp_sentEmail(Request $request)
    {
        return view('etiket.in.lupapw-sent email');
    }
    public function lp_confirmEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        $credentials = $request->only('email');


        $token = $this->generateRandomString(30);
        if (DB::table("password_reset_tokens")->where('email', $credentials['email'])->exists()) {
            DB::table('password_reset_tokens')
                ->where('email', $credentials['email'])
                ->update(['token' => $token]);
        } else {
            DB::table("password_reset_tokens")->insert([
                "email" => $credentials['email'],
                "token" => $token
            ]);
        }

        $userExists = User::where('email', $credentials['email'])->exists();
        if (!$userExists) {
            return back()->withErrors([
                'email' => 'The provided email does not match our records.',
            ]);
        }

        Mail::to($credentials['email'])->send(new MyTestEmail($token));
        return view('etiket.in.lupapw-confm email', ['email' => $credentials['email']]);
    }


    function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
