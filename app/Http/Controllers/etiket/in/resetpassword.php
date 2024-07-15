<?php

namespace App\Http\Controllers\etiket\in;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\MyTestEmail;
use Illuminate\Support\Facades\Mail;

class resetpassword extends Controller
{
    public function resetpassword($token)
    {
        return view('etiket.in.reset-password'); // Ganti 'register' dengan nama view yang sesuai
    }

    public function actionresetpassword(Request $request, $email)
    {

        Mail::to("mukhtadanasution@gmail.com")->send(new MyTestEmail("Halo"));

        return redirect()->route('etiket.in.login')->with(
            'success',
            'berhasil berhasil reset password'
        );
        // return back()->withErrors([
        //     'password' => 'Password tidak sesuai',
        // ]);
    }
}
