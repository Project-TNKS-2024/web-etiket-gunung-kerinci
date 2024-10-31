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
use Illuminate\Database\Eloquent\Model;

class destinasiController extends Controller
{
    public function detail($id)
    {
        $destinasi = ModelsDestinasi::where('id', $id)->first();
        $gambar = ModelGambar::with(['destinasi'])->where('id_destinasi', $id)->get();
        $gates = ModelGates::with(['destinasi'])->where('gk_gates.id_destinasi', $id)->get();

        // return $gates;

        return view('etiket.admin.destinasi.destinasi.detail', [
            'destinasi' => $destinasi,
            'gates' => $gates,
            'gambar' => $gambar,

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
            'id_destinasi' => 'required',
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
                    "id_destinasi" => $request->id_destinasi,
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
    public function pictureDeleteAction(Request $request)
    {
        $request->validate([
            'id_destinasi' => 'required',
            'id_gambar' => 'required',
        ]);
        // cek gambar dalm database
        $foto = ModelGambar::where('id', $request->id_gambar)->where('id_destinasi', $request->id_destinasi)->first();
        // hapus gambar dalam asset
        $uploadController = new uploadFileControlller();
        $del = $uploadController->delete($foto->src);
        // hapus gambar dari tabel
        $foto->delete();
        return back()->with('success', 'Berhasil Menghapus Gambar');
    }
    public function gatesAddAction(Request $request)
    {
        $request->validate([
            'id_destinasi' => 'required|exists:destinasis,id',
            'nama' => 'required|string|max:255',
            'status' => 'required|in:1,0',
            'max_pendaki_hari' => 'required|integer|min:1',
            'min_pendaki_booking' => 'required|integer|min:1',
            'lokasi' => 'required|string|max:255',
            'lokasi_maps' => 'nullable|string|max:500',
            'detail' => 'nullable|string|max:1000'
        ]);
        ModelGates::create([
            'id_destinasi' => $request->id_destinasi,
            'nama' => $request->nama,
            'status' => $request->status,
            'max_pendaki_hari' => $request->max_pendaki_hari,
            'min_pendaki_booking' => $request->min_pendaki_booking,
            'lokasi' => $request->lokasi,
            'lokasi_maps' => $request->lokasi_maps,
            'detail' => $request->detail,
        ]);
        return back()->with('success', 'Berhasil menambah gate');
    }
    public function gatesUpdate($id)
    {
        $gate = ModelGates::find($id);
        $destinasi = ModelsDestinasi::find($gate->id_destinasi);
        return view('etiket.admin.destinasi.destinasi.gateUpdate', [
            'gate' => $gate,
            'destinasi' => $destinasi,
        ]);
    }
    public function gatesUpdateAction(Request $request)
    {
        $request->validate([
            'id_gate' => 'required|exists:gk_gates,id',
            'nama' => 'required|string|max:255',
            'status' => 'required|in:1,0',
            'max_pendaki_hari' => 'required|integer|min:1',
            'min_pendaki_booking' => 'required|integer|min:1',
            'lokasi' => 'required|string|max:255',
            'lokasi_maps' => 'nullable|string|max:500',
            'detail' => 'nullable|string|max:1000'
        ]);

        $gate = ModelGates::find($request->id_gate);
        $gate->update([
            'nama' => $request->nama,
            'status' => $request->status,
            'max_pendaki_hari' => $request->max_pendaki_hari,
            'min_pendaki_booking' => $request->min_pendaki_booking,
            'lokasi' => $request->lokasi,
            'lokasi_maps' => $request->lokasi_maps,
            'detail' => $request->detail,
        ]);

        return back()->with('success', 'Berhasil update gate');
    }

    public function gatesDeleteAction(Request $request)
    {
        $request->validate([
            'id_destinasi' => 'required',
            'id_gate' => 'required',
        ]);
        $gate = ModelGates::where('id', $request->id_gate)->where('id_destinasi', $request->id_destinasi)->first();
        $gate->delete();
        return back()->with('success', 'Berhasil Menghapus Gambar');
    }
}
