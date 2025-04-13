<?php

namespace App\Http\Controllers\etiket\admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\helper\uploadFileControlller;
use App\Models\bio_pendaki;
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

    public function update(Request $request)
    {
        $auth = Auth::user();
        $user = User::with('biodata')->find($auth->id);

        // return $request;
        // return $user;
        $request->validate([
            'lampiran_identitas' => 'required|file|mimes:jpg,jpeg,png,pdf|max:548',
            'nomor_telepon' => 'required|numeric',
            'telp_country' => 'required|string|max:5',
            'jenis_kelamin' => 'required|in:l,p',
            'tanggal_lahir' => 'required|date|before:today',
            'provinsi' => 'numeric',
            'kabupaten_kota' => 'numeric',
            'kecamatan' => 'numeric',
            'desa_kelurahan' => 'numeric',
        ]);

        // Format nomor telepon
        if ($request->nomor_telepon[0] == 0) {
            $request['nomor_telepon'] = substr($request->nomor_telepon, 1);
        }
        $request['nomor_telepon'] = $request->telp_country . ' ' . $request->nomor_telepon;

        // cari biodata dengan nik yang sama
        $bioUseNik = bio_pendaki::where('nik', $request->nik)->where('verified', 'verified')->first();
        if ($bioUseNik && $bioUseNik->id != $user->id_bio) {
            return redirect()->back()->with('error', 'NIK sudah digunakan');
        }

        // deklarasi upload file
        $upload = new uploadFileControlller();

        // update biodata
        $bio = bio_pendaki::find($user->id_bio);
        if (!$bio) {
            return redirect()->back()->with('error', 'Biodata tidak ditemukan');
        }

        $filename = $upload->upadate($bio->lampiran_identitas, $request->file('lampiran_identitas'));
        if (!$filename) {
            $filename = $upload->create($user->id, 'identitas', $request->file('lampiran_identitas'));
        }

        $bio->update([
            'lampiran_identitas' => $filename,
            'no_hp' => $request->nomor_telepon,
            'no_hp_darurat' => null,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tanggal_lahir' => $request->tanggal_lahir,
            'provinsi' => $request->kewarganegaraan == 'ID' ? $request->provinsi : null,
            'kabupaten' => $request->kewarganegaraan == 'ID' ? $request->kabupaten_kota : null,
            'kec' => $request->kewarganegaraan == 'ID' ? $request->kecamatan : null,
            'desa' => $request->kewarganegaraan == 'ID' ? $request->desa_kelurahan : null,
            'verified' => 'pending',
        ]);

        return back()->with('success', 'Berhasil mengubah data');
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
