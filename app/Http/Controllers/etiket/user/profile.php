<?php

namespace App\Http\Controllers\etiket\user;

use App\Http\Controllers\Controller;
use App\Http\Controllers\helper\uploadFileControlller;
use App\Models\bio_pendaki;
use App\Models\Data\Negara;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class profile extends Controller
{
    public function index()
    {
        $auth = Auth::user();
        $user = User::with('biodata')->find($auth->id);
        $negara = Negara::getAll();

        // dd($negara);
        // return $negara;

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

        // return $user;

        return view('etiket.user.sections.profile', [
            'user' => $user,
            'negara' => $negara,
        ]);
    }

    public function action(Request $request)
    {
        $auth = Auth::user();
        $user = User::with('biodata')->find($auth->id);

        // return $user;
        $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'string|max:255|nullable',
            'lampiran_identitas' => 'required|file|mimes:jpg,jpeg,png,pdf|max:548',
            'kewarganegaraan' => 'required|string',
            'nik' => 'required|numeric|min:8',
            'nomor_telepon' => 'required|numeric',
            'telp_country' => 'required|string|max:5',
            'jenis_kelamin' => 'required|in:l,p',
            'tanggal_lahir' => 'required|date|before:today',
            'provinsi' => 'required|numeric',
            'kabupaten_kota' => 'required|numeric',
            'kecamatan' => 'required|numeric',
            'desa_kelurahan' => 'required|numeric',
        ]);
        // Format nomor telepon
        if ($request->nomor_telepon[0] == 0) {
            $request['nomor_telepon'] = substr($request->nomor_telepon, 1);
        }
        $request['nomor_telepon'] = $request->telp_country . ' ' . $request->nomor_telepon;



        $upload = new uploadFileControlller();

        // cek apakah sudah ada bio atau belum
        $bio = null;
        if ($user->id_bio) {
            $bio = bio_pendaki::find($user->id_bio);

            if (!$bio->verified_at) {
                $bioUseNik = bio_pendaki::where('nik', $request->nik)->where('verified', 'verified')->first();

                if ($bioUseNik) {
                    // return $bioUseNik;
                    return redirect()->back()->with('error', 'NIK sudah digunakan');
                }
                $bio->update([
                    'nik' => $request->nik,
                ]);
            }

            $filename = $upload->upadate($bio->lampiran_identitas, $request->file('lampiran_identitas'));
            if (!$filename) {
                $filename = $upload->create($user->id, 'identitas', $request->file('lampiran_identitas'));
            }

            $bio->update([
                'kenegaraan' => $request->kewarganegaraan,
                'first_name' => $request->firstName,
                'last_name' => $request->lastName,
                'lampiran_identitas' => $filename,
                'no_hp' => $request->nomor_telepon,
                'no_hp_darurat' => null,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tanggal_lahir' => $request->tanggal_lahir,
                'provinsi' => $request->provinsi,
                'kabupaten' => $request->kabupaten_kota,
                'kec' => $request->kecamatan,
                'desa' => $request->desa_kelurahan,
                'verified' => 'pending',
            ]);
        } else {
            $filename = $upload->create($user->id, 'identitas', $request->file('lampiran_identitas'));
            $bio = bio_pendaki::create([
                'nik' => $request->nik,
                'kenegaraan' => $request->kewarganegaraan,
                'first_name' => $request->firstName,
                'last_name' => $request->lastName,
                'lampiran_identitas' => $filename,
                'no_hp' => $request->nomor_telepon,
                'no_hp_darurat' => "",
                'jenis_kelamin' => $request->jenis_kelamin,
                'tanggal_lahir' => $request->tanggal_lahir,
                'provinsi' => $request->provinsi,
                'kabupaten' => $request->kabupaten_kota,
                'kec' => $request->kecamatan,
                'desa' => $request->desa_kelurahan,
                'verified' => 'pending',
            ]);
        }

        $user->id_bio = $bio->id;
        $user->save();


        return back()->with('success', 'Berhasil mengubah data');
    }
}
