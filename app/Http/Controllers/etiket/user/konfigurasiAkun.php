<?php

namespace App\Http\Controllers\etiket\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

use App\Http\Controllers\helper\uploadFileControlller;

class konfigurasiAkun extends Controller
{
    //

    public function index()
    {

        return view('etiket.user.sections.akun');
    }

    public function action(Request $request)
    {
        $auth = Auth::user();

        // return $user;
        $request->validate([
            'avatar' => 'required|file|mimes:jpg,jpeg,png|max:548',
        ]);

        //ganti foto profile
        $upload = new uploadFileControlller();

        if ($request->file('avatar')) {
            $filename = $auth->avatar;

            if ($filename == null) {
                $filename = $upload->create($auth->id, 'avatar', $request->file('avatar'));
            } else {
                $filename = $upload->upadate($auth->avatar, $request->file('avatar'));
            }

            $auth->update([
                'avatar' => $filename,
            ]);

            return redirect()->back()->with('success', "$filename");
        }

        return redirect()->back()->with('error', 'Gagal melakukan perubahan');
    }
}
