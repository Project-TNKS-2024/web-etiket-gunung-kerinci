<?php

namespace App\Http\Controllers\etiket\admin\destinasi;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

use App\Models\destinasi as ModelsDestinasi;
use App\Models\gambar_destinasi as ModelGambar;
use App\Models\gk_gates as ModelGates;

use App\Http\Controllers\helper\uploadFileControlller;


class destinasiController extends AdminController
{
    public function detail($id)
    {
        $destinasi = ModelsDestinasi::where('id', $id)->first();
        $gambar = ModelGambar::with(['destinasi'])
            ->where('id_destinasi', $id)
            ->get();
        $gates = ModelGates::with(['destinasi'])
            ->where('gk_gates.id_destinasi', $id)
            ->get();

        // return $destinasi;

        return view('etiket.admin.destinasi.destinasi.detail', [
            'destinasi' => $destinasi,
            'gates' => $gates,
            'gambar' => $gambar,
        ]);
    }

    public function destinasiUpdate($id)
    {
        $destinasi = ModelsDestinasi::find($id);
        return view('etiket.admin.destinasi.destinasi.destinasiUpdate', [
            'destinasi' => $destinasi,
        ]);
    }

    public function destinasiUpdateAction(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'nama' => 'required',
            'kategori' => 'required',
            'status' => 'required',
            'statusGunung' => 'required',
            'lokasi' => 'required',
            'detail' => 'required',
            'sop' => 'required',
        ]);

        // return $request;
        if (
            !ModelsDestinasi::where('id', $request->id)->update([
                'nama' => $request->nama,
                'status' => $request->status,
                'statusGunung' => $request->status,
                'kategori' => $request->kategori,
                'lokasi' => $request->lokasi,
                'detail' => $request->detail,
                'sop' => $request->sop,
            ])
        ) {
            return back()->withErrors(['database', 'Terjadi kesalahan saat mengubah destinasi']);
        }

        return back()->with('success', 'Berhasil memperbarui tiket');
    }
    public function pictureAddAction(Request $request)
    {
        // Validate the request
        $request->validate([
            'id_destinasi' => 'required',
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5048', // Added max size constraint
            'foto_nama' => 'required|string|max:255', // Added max length constraint
            'foto_detail' => 'required|string|max:500', // Added max length constraint
        ]);

        try {
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');

                $uploadController = new uploadFileControlller();
                $fileUrl = $uploadController->create('img', 'destinasi', $file);

                ModelGambar::create([
                    'src' => $fileUrl,
                    'nama' => $request->input('foto_nama'),
                    'detail' => $request->input('foto_detail'),
                    'id_destinasi' => $request->id_destinasi,
                ]);

                return back()->with('success', 'Berhasil mengupload gambar.');
            } else {
                return back()->withErrors(['foto' => 'File tidak ditemukan.']);
            }
        } catch (Exception $e) {
            // Log::error('Error uploading file: ' . $e->getMessage());
            return back()->withErrors('Terjadi kesalahan saat mengupload gambar: ' . $e->getMessage());
        }
    }
    public function pictureDeleteAction(Request $request)
    {
        $request->validate([
            'id_destinasi' => 'required',
            'id_gambar' => 'required',
        ]);
        // cek gambar dalm database
        $foto = ModelGambar::where('id', $request->id_gambar)
            ->where('id_destinasi', $request->id_destinasi)
            ->first();
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
            'detail' => 'nullable|string|max:1000',
            'qris' => 'required|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $uploadController = new uploadFileControlller();

        $new_gate = new ModelGates();
        $new_gate->id_destinasi = $request->id_destinasi;
        $new_gate->nama = $request->nama;
        $new_gate->status = $request->status;
        $new_gate->max_pendaki_hari = $request->max_pendaki_hari;
        $new_gate->min_pendaki_booking = $request->min_pendaki_booking;
        $new_gate->lokasi = $request->lokasi;
        $new_gate->lokasi_maps = $request->lokasi_maps;
        $new_gate->detail = $request->detail;
        $qris_path = $uploadController->create('foto', 'qris', $request->file('qris'));
        $new_gate->qris = $qris_path;
        $new_gate->save();

        return back()->with('success', 'Berhasil menambah gate');
    }

    public function gatesUpdate($id)
    {
        $gate = ModelGates::find($id);
        $destinasi = ModelsDestinasi::find($gate->id_destinasi);

        // return $gate;
        return view('etiket.admin.destinasi.destinasi.gateUpdate', [
            'gate' => $gate,
            'destinasi' => $destinasi,
        ]);
    }
    public function gatesUpdateAction(Request $request)
    {
        // Validation
        $request->validate([
            'id_gate' => 'required|exists:gk_gates,id',
            'nama' => 'required|string|max:255',
            'status' => 'required|in:1,0',
            'max_pendaki_hari' => 'required|integer|min:1',
            'min_pendaki_booking' => 'required|integer|min:1',
            'lokasi' => 'required|string|max:255',
            'lokasi_maps' => 'nullable|string|max:500',
            'detail' => 'nullable|string|max:1000',
            'qris' => 'nullable|image|mimes:jpg,png,jpeg|max:1048', // Make QRIS optional
        ]);

        // return $request;
        // Find the gate
        $gate = ModelGates::find($request->id_gate);

        $dataUpdate = [
            'nama' => $request->nama,
            'status' => $request->status,
            'max_pendaki_hari' => $request->max_pendaki_hari,
            'min_pendaki_booking' => $request->min_pendaki_booking,
            'lokasi' => $request->lokasi,
            'lokasi_maps' => $request->lokasi_maps,
            'detail' => $request->detail,
        ];

        if ($request->hasFile('qris')) {
            $uploadController = new uploadFileControlller();
            if ($gate->qris) {
                $path = $uploadController->upadate($gate->qris->path, $request->file('qris'));
                $dataUpdate['qris'] = $path;
            } else {
                $path = $uploadController->create('foto', 'qris', $request->file('qris'));
                $dataUpdate['qris'] = $path;
            }
        }

        $gate->update($dataUpdate);


        // Redirect with success message
        return back()->with('success', 'Berhasil update gate');
    }

    public function gatesDeleteAction(Request $request)
    {
        $request->validate([
            'id_destinasi' => 'required',
            'id_gate' => 'required',
        ]);
        $gate = ModelGates::where('id', $request->id_gate)
            ->where('id_destinasi', $request->id_destinasi)
            ->first();
        $gate->delete();
        return back()->with('success', 'Berhasil Menghapus Gate');
    }
}
