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
    public function index(Request $request)
    {
        $query = User::where('role', 'user')
            ->join('biodatas', 'users.id_bio', '=', 'biodatas.id')
            ->orderByRaw("FIELD(biodatas.verified, 'pending', 'verified', 'unverified')")
            ->orderBy('biodatas.first_name')
            ->select('users.*')
            ->with('biodata');

        // Cek jika ada pencarian
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('users.email', 'like', "%$search%")
                    ->orWhere('biodatas.first_name', 'like', "%$search%")
                    ->orWhere('biodatas.last_name', 'like', "%$search%")
                    ->orWhere('biodatas.id', 'like', "%$search%");
            });
        }

        $dataUser = $query->paginate(50);

        return view('etiket.admin.master.akunUsers.index', compact('dataUser'));
    }

    public function biodata($id)
    {
        $user = User::with('booking.destinasi', 'booking.pendakis', 'biodata')->where('role', 'user')->find($id);
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
                // cek nik sudsha disunakan atau belm
                $bioUseNik = bio_pendaki::where('nik', $biodata->nik)->where('verified', 'verified')->first();
                if ($bioUseNik && $bioUseNik->id != $user->id_bio) {
                    return redirect()->back()->with('error', 'NIK sudah digunakan di akun lain');
                }

                // verifikasi
                $biodata->verified = 'verified';
                $biodata->verified_at = now();
                $status = 'verified';
            } elseif ($request->verified == 'unverified') {
                $biodata->verified = 'unverified';
                $status = 'unverified';
            }
            $biodata->keterangan = $request->keterangan;
            $biodata->validator =  $this->userAdmin()->id;
            $biodata->save();

            // Kirim email ke user
            try {
                Mail::to($biodata->user->email)->send(new BiodataVerifiedMail($biodata, $status));
            } catch (\Exception $e) {
                Log::channel('admin')->error('Gagal mengirim email verifikasi biodata.', [
                    'error' => $e->getMessage(),
                    'user_email' => $biodata->user->email,
                    'user_id' => $biodata->user->id,
                    'biodata_id' => $biodata->id,
                    'nik' => $biodata->nik,
                    'status' => $status,
                ]);
            }
            return redirect()->back()->with('success', 'Data berhasil diubah');
        }
        return redirect()->back()->with('error', 'Data tidak valid');
    }
}
