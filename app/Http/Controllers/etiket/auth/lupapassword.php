<?php

namespace App\Http\Controllers\etiket\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\MyTestEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class lupapassword extends Controller
{
    public function sentEmail()
    {
        // return view('etiket.auth.lupapw-sent email');
        return view('etiket.auth.lupa-password');
        // return view('etiket.auth.lupa-confirm');
    }
    public function confirmEmail(Request $request)
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

        // return $credentials['email'];

        Mail::to($credentials['email'])->send(new MyTestEmail($token));
        return view('etiket.auth.lupa-confirm', ['email' => $credentials['email']]);
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
