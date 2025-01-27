<?php

namespace App\Http\Controllers\etiket\admin\master;

use App\Http\Controllers\Controller;
use App\Models\bio_pendaki;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PengunjungController extends Controller
{
    public function index()
    {
        $dataUser = User::where('role', 'user')
            ->join('biodatas', 'users.id_bio', '=', 'biodatas.id')
            ->orderByRaw("FIELD(biodatas.verified, 'pending', 'verified', 'unverified')")
            ->orderBy('biodatas.first_name')
            ->select('users.*')
            ->with('biodata')
            ->paginate(50);

        // return $dataUser;

        return view('etiket.admin.master.akunUsers.index', compact('dataUser'));
    }
    public function verificationBiodata(Request $request)
    {
        // validasi
        $request->validate([
            'id_user' => 'required',
            'verified' => 'required',
            'keterangan' => 'string|nullable|max:255',
        ]);

        // return $request;
        $user = User::find($request->id_user);
        $biodata = bio_pendaki::where('id', $user->id_bio)->first();

        // return $biodata;
        if ($biodata->verified == 'pending') {
            if ($request->verified == 'verified') {
                $biodata->verified = 'verified';
                $biodata->verified_at = now();
            } elseif ($request->verified == 'unverified') {
                $biodata->verified = 'unverified';
            }
            $biodata->keterangan = $request->keterangan;
            $biodata->save();
        } else {
            return redirect()->back()->with('error', 'Data tidak valid');
        }

        return redirect()->back()->with('success', 'Data berhasil diubah');
    }
}
