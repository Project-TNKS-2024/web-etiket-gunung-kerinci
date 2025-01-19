<?php

namespace App\Http\Controllers\etiket\admin\master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\pengajuan;

class ValidasiPembayaran extends Controller
{
    //
    public function index()
    {
        $pengajuan = pengajuan::orderBy('created_at', 'desc')->get();
        return view('etiket.admin.master.validasi.daftar', compact('pengajuan'));
    }

    public function updateAction(Request $request)
    {

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'keterangan' => 'string|nullable|max:255',
            'pengajuanId' => 'required|integer'
        ]);

        $pengajuan = pengajuan::findOrFail($request->pengajuanId);
        $pengajuan->status = $request->status;
        $pengajuan->keterangan = $request->keterangan ?? "";

        // dd($request->all());
        $pengajuan->save();

        return redirect()->route('admin.master.validasi.daftar')->with('success', 'Pengajuan berhasil diperbarui');
    }
}
