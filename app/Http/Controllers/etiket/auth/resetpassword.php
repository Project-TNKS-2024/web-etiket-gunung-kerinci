<?php

namespace App\Http\Controllers\etiket\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\MyTestEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class resetpassword extends Controller
{
    public function resetpassword($token)
    {
        $tokenExists = DB::table('password_reset_tokens')->where('token', $token)->exists();
        if (!$tokenExists) {
            abort(404);
        }
        return view('etiket.auth.reset-password', ['token' => $token]); // Ganti 'register' dengan nama view yang sesuai
    }

    public function actionresetpassword(Request $request)
    {
        // Validate the request data
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
            'token' => 'required|string'
        ], [
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        $token = $request->token;

        $record = DB::table('password_reset_tokens')->where('token', $token)->first();
        if (!$record) {
            abort(404);
        }
        $email = $record->email;

        $user = User::where('email', $email)->firstOrFail();

        $user->password = bcrypt($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('token', $token)->delete();
        return redirect()->route('login')->with('success', 'Berhasil berhasil reset password');
    }
}
