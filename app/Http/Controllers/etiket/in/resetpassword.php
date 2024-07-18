<?php

namespace App\Http\Controllers\etiket\in;

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
        return view('etiket.in.reset-password', ['token' => $token]); // Ganti 'register' dengan nama view yang sesuai
    }

    public function actionResetPassword(Request $request, $token)
    {
        // Validate the request data
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        // Retrieve the email associated with the token and check if the token exists
        $record = DB::table('password_reset_tokens')->where('token', $token)->first();

        // If no record is found, abort with a 404 error
        if (!$record) {
            abort(404);
        }

        // Retrieve the email from the record
        $email = $record->email;

        // Retrieve the user by email
        $user = User::where('email', $email)->firstOrFail();

        // Update the user's password
        $user->password = bcrypt($request->password);
        $user->save();

        // Optionally, delete the token after successful reset
        DB::table('password_reset_tokens')->where('token', $token)->delete();

        // Redirect to the login page with a success message
        return redirect()->route('etiket.in.login')->with('success', 'Berhasil berhasil reset password');
    }
}
