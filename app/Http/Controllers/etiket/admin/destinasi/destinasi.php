<?php

namespace App\Http\Controllers\etiket\admin\destinasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

use App\Models\destinasi as ModelsDestinasi;
use App\Models\gambar_destinasi as ModelGambar;
use App\Models\gk_gates as ModelGates;
use App\Models\gk_tiket_pendaki as ModelPendaki;

use App\Http\Controllers\helper\uploadFileControlller;

class destinasi extends Controller
{
    public function detail($id)
    {
        $destinasi = ModelsDestinasi::where('id', $id)->first();
        $gambar = ModelGambar::with(['destinasi'])->where('id_destinasi', $id)->get();
        $gates = ModelGates::with(['destinasi'])->where('gk_gates.id_destinasi', $id)->get();
        $tiket = ModelPendaki::with(['paket_tiket'])->get();

        return view('etiket.admin.destinasi.destinasi.detail', [
            'destinasi' => $destinasi,
            'gates' => $gates,
            'gambar' => $gambar,
            'tiket' => $tiket
        ]);
    }
    public function detailUpdate(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required',
            'status' => 'required',
            'kategori' => 'required',
            'lokasi' => 'required',
            'detail' => 'required',
            'status' => 'required',
        ]);

        // return $request;

        if (!ModelsDestinasi::where('id', $id)->update([
            'nama' => $request->nama,
            'status' => $request->status,
            'kategori' => $request->kategori,
            'lokasi' => $request->lokasi,
            'detail' => $request->detail,
        ])) {
            return back()->withErrors(['database', 'Terjadi kesalahan saat mengubah destinasi']);
        }

        return back()->with('success', 'Berhasil memperbarui tiket');
    }
    public function pictureAddAction(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Added max size constraint
            'foto_nama' => 'required|string|max:255', // Added max length constraint
            'foto_detail' => 'required|string|max:500', // Added max length constraint
        ]);

        try {
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');


                $uploadController = new uploadFileControlller();
                $fileUrl = $uploadController->create('img', 'destinasi', $file);

                ModelGambar::create([
                    "src" => $fileUrl,
                    "nama" => $request->input('foto_nama'),
                    "detail" => $request->input('foto_detail'),
                    "id_destinasi" => $id,
                ]);

                return back()->with('success', 'Berhasil mengupload gambar.');
            } else {
                return back()->withErrors(['foto' => 'File tidak ditemukan.']);
            }
        } catch (Exception $e) {
            Log::error('Error uploading file: ' . $e->getMessage());
            return back()->withErrors(['database' => 'Terjadi kesalahan saat mengupload gambar: ' . $e->getMessage()]);
        }
    }
}
