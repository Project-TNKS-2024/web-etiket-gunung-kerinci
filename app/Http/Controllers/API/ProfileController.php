<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\helper\ApiResponse;
use App\Http\Controllers\helper\uploadFileControlller;
use App\Models\bio_pendaki;
use App\Models\gk_pendaki;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function getPendakiIdentity(Request $request)
    {
        $user = $request->user();
        $biodata = $user->id_bio ? bio_pendaki::find($user->id_bio) : null;

        $pendakiIds = $user->id_bio
            ? gk_pendaki::where('id_bio', $user->id_bio)
                ->pluck('id')
                ->values()
            : collect();

        $data = [
            'id_user' => $user->id,
            'id_bio' => $user->id_bio,
            'status_verifikasi' => $biodata?->verified ?? 'unverified',
            'verified_at' => $biodata?->verified_at,
            'pendaki_ids' => $pendakiIds,
        ];

        return ApiResponse::success($data, 'Berhasil mengambil identitas pendaki', 200);
    }

    public function getBiodata(Request $request)
    {
        $user = $request->user();
        $biodata = bio_pendaki::find($user->id_bio);

        // return $user;
        // return $biodata;
        return ApiResponse::success($biodata, "Berhasil mengambil biodata", 200);
    }

    public function updateBiodata(Request $request)
    {
        $user = $request->user();

        $isUpdate = (bool) $user->id_bio;
        $isIndonesia = $request->input('kewarganegaraan') === 'ID';

        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string|max:255',
            'lastName' => 'nullable|string|max:255',
            'lampiran_identitas' => ($isUpdate ? 'nullable' : 'required') . '|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'kewarganegaraan' => 'required|string',
            'nik' => 'required|string|min:6|max:16|alpha_num',
            'nomor_telepon' => 'required|numeric',
            'telp_country' => 'required|string|max:5',
            'jenis_kelamin' => 'required|in:l,p',
            'tanggal_lahir' => 'required|date|before:today',
            'provinsi' => ($isIndonesia ? 'required' : 'nullable') . '|numeric',
            'kabupaten_kota' => ($isIndonesia ? 'required' : 'nullable') . '|numeric',
            'kecamatan' => ($isIndonesia ? 'required' : 'nullable') . '|numeric',
            'desa_kelurahan' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Validasi gagal', $validator->errors(), 422);
        }

        $validated = $validator->validated();

        // return $user;

        // Format nomor HP
        $noHp = ltrim($validated['nomor_telepon'], '0');
        $noHp = $validated['telp_country'] . ' ' . $noHp;

        // Cek NIK
        $bioUseNik = bio_pendaki::where('nik', $validated['nik'])
            ->where('verified', 'verified')
            ->first();

        if ($bioUseNik && $bioUseNik->id != $user->id_bio) {
            return ApiResponse::error('NIK sudah digunakan', 409);
        }

        $upload = new uploadFileControlller();

        if ($user->id_bio) {
            $bio = bio_pendaki::findOrFail($user->id_bio);

            if (!$bio->verified_at) {
                $bio->nik = $validated['nik'];
            }

            $filename = $request->hasFile('lampiran_identitas')
                ? ($upload->upadate($bio->lampiran_identitas, $request->file('lampiran_identitas'))
                    ?? $upload->create($user->id, 'identitas', $request->file('lampiran_identitas')))
                : $bio->lampiran_identitas;

            $bio->update([
                'kenegaraan' => $validated['kewarganegaraan'],
                'first_name' => $validated['firstName'],
                'last_name' => $validated['lastName'],
                'lampiran_identitas' => $filename,
                'no_hp' => $noHp,
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'provinsi' => $validated['kewarganegaraan'] == 'ID' ? $validated['provinsi'] : null,
                'kabupaten' => $validated['kewarganegaraan'] == 'ID' ? $validated['kabupaten_kota'] : null,
                'kec' => $validated['kewarganegaraan'] == 'ID' ? $validated['kecamatan'] : null,
                'desa' => $validated['kewarganegaraan'] == 'ID' ? ($validated['desa_kelurahan'] ?? null) : null,
                'verified' => 'pending',
            ]);
        } else {
            $filename = $upload->create($user->id, 'identitas', $request->file('lampiran_identitas'));

            $bio = bio_pendaki::create([
                'nik' => $validated['nik'],
                'kenegaraan' => $validated['kewarganegaraan'],
                'first_name' => $validated['firstName'],
                'last_name' => $validated['lastName'],
                'lampiran_identitas' => $filename,
                'no_hp' => $noHp,
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'provinsi' => $validated['kewarganegaraan'] == 'ID' ? $validated['provinsi'] : null,
                'kabupaten' => $validated['kewarganegaraan'] == 'ID' ? $validated['kabupaten_kota'] : null,
                'kec' => $validated['kewarganegaraan'] == 'ID' ? $validated['kecamatan'] : null,
                'desa' => $validated['kewarganegaraan'] == 'ID' ? ($validated['desa_kelurahan'] ?? null) : null,
                'verified' => 'pending',
            ]);

            $user->update(['id_bio' => $bio->id]);
        }

        return ApiResponse::success($bio, 'Berhasil mengubah data', 200);
    }


    public function gantiPassword(Request $request)
    {
        // VALIDASI (API-SAFE)
        $validator = Validator::make(
            $request->all(),
            [
                'password_baru' => 'required|string|min:8|confirmed',
            ],
            [
                'password_baru.required' => 'Password harus diisi.',
                'password_baru.min' => 'Password minimal :min karakter.',
                'password_baru.confirmed' => 'Konfirmasi password tidak cocok.',
            ]
        );

        if ($validator->fails()) {

            // return response()->json([
            //     'success' => false,
            //     'message' => 'Validasi gagal',
            //     'errors'  => $validator->errors(),
            // ], 422);


            return ApiResponse::error('Validasi gagal', $validator->errors(), 422);
        }

        $user = $request->user();

        // UPDATE PASSWORD
        $user->password = Hash::make($request->password_baru);
        $user->save();

        return ApiResponse::success(null, 'Password berhasil diperbarui', 200);
    }
}
