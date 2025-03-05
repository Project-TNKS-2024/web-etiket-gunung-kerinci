<?php

namespace App\Http\Controllers\etiket\admin\master;

use App\Http\Controllers\AdminController;
use App\Mail\BiodataVerifiedMail;
use App\Models\bio_pendaki;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PengunjungController extends AdminController
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

        return view('etiket.admin.master.akunUsers.index', compact('dataUser'));
    }
    public function biodata($id)
    {
        $user = User::with('booking.destinasi', 'booking.pendakis', 'biodata')->where('role', 'user')->find($id);

        // return $user;
        // return $user->biodata->dataDesa->name;
        return view('etiket.admin.master.akunUsers.biodata', compact('user'));
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
                try {
                    Mail::to($biodata->user->email)->send(new BiodataVerifiedMail($biodata));
                } catch (\Exception $e) {
                    Log::channel('admin')->error(
                        'Terjadi kesalahan pada proses booking kirim email pembelian booking ke ' . $biodata->user->email,
                        [
                            'admin' => Auth::user(),
                            'pengguna' => $biodata->user,
                            'error' => $e->getMessage()
                        ]
                    );
                }
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
