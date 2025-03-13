<?php

namespace App\Http\Controllers\etiket\admin;

use App\Http\Controllers\Controller;
use App\Models\Data\Negara;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $auth = Auth::user();
        $user = User::with('biodata')->find($auth->id);
        $negara = Negara::getAll();


        // Pisahkan kode negara dari nomor telepon
        $telp_country = explode(" ", $user->biodata->no_hp ?? '');

        // Validasi format nomor telepon
        if ($user->biodata) {
            if (count($telp_country) === 2) {
                $user->biodata->no_hp = $telp_country[1];
                $user->biodata->telp_country = $telp_country[0];
            } else {
                $user->biodata->telp_country = '';
                // Tetap gunakan no_hp asli
            }
        }

        return view('etiket.admin.profile.index', [
            'user' => $user,
            'negara' => $negara,
        ]);
    }

    public function resetPassword()
    {
        return view('etiket.admin.profile.resetPassword');
    }

    public function resetPasswordAction(Request $request)
    {
        // Validasi input
        $request->validate([
            'password_baru' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Ambil user yang sedang login
        $user = Auth::user();

        // cek user role == admin
        if ($user->role !== 'admin') {
            Auth::logout(); // Logout user
            return redirect()->route('login')->with('error', 'Anda bukan admin. Silakan login kembali.');
        }

        // Update password user
        $user->update([
            'password' => Hash::make($request->password_baru),
        ]);

        // Redirect dengan pesan sukses
        return redirect()->route('admin.profile')->with('success', 'Password berhasil direset.');
    }
}
