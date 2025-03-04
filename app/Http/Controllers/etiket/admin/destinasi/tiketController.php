<?php

namespace App\Http\Controllers\etiket\admin\destinasi;

use App\Http\Controllers\AdminController;
use App\Models\destinasi as ModelDestinasi;
use App\Models\gk_paket_tiket as ModelPaketTiket;
use App\Models\gk_tiket_pendaki as ModelTiket;
use Illuminate\Http\Request;

class tiketController extends AdminController
{
    public function tiket($id)
    {
        $data = ModelPaketTiket::with(['tiket_pendaki'])->where('id_destinasi', $id)->get();

        // return $data;

        return view('etiket.admin.destinasi.tiket.tiket', [
            "tiket" => $data,
            "id_destinasi" => $id
        ]);
    }
    public function add($id)
    {
        // return $id;
        $destinasi = ModelDestinasi::find($id);


        return view('etiket.admin.destinasi.tiket.tiketUpdate', [
            // return view('etiket.admin.destinasi.tiket.tiketAdd', [
            "destinasi" => $destinasi,
        ]);
    }
    public function addAction(Request $request)
    {
        // Step 1: Validate input
        $request->validate([
            'id_destinasi' => 'required',
            'destinasi' => 'required|string|max:255',
            'nama_paket' => 'required|string|max:255',
            'min_pendaki' => 'required|integer|min:1',
            'title_penugasan' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',

            'harga_masuk_weekend_wni' => 'required|integer|min:0',
            'harga_masuk_weekday_wni' => 'required|integer|min:0',
            'harga_kemah_wni' => 'nullable|integer|min:0',
            'harga_tracking_wni' => 'nullable|integer|min:0',
            'harga_asuransi_wni' => 'nullable|integer|min:0',

            'harga_masuk_weekend_wna' => 'required|integer|min:0',
            'harga_masuk_weekday_wna' => 'required|integer|min:0',
            'harga_kemah_wna' => 'nullable|integer|min:0',
            'harga_tracking_wna' => 'nullable|integer|min:0',
            'harga_asuransi_wna' => 'nullable|integer|min:0',
        ]);

        // step 2: input paket
        $paketTiket = ModelPaketTiket::create([
            'nama' => $request->input('nama_paket'),
            'min_pendaki' => $request->input('min_pendaki'),
            'penugasan' => $request->input('title_penugasan'),
            'keterangan' => $request->input('keterangan'),
            'id_destinasi' => $request->input('id_destinasi'),  // Assuming destinasi ID is passed here
        ]);
        // step 3: input price tiket wni
        ModelTiket::create([
            'id_paket_tiket' => $paketTiket->id,
            'kategori_pendaki' => 'WNI',
            'harga_masuk_wk' => $request->input('harga_masuk_weekend_wni'),
            'harga_masuk_wd' => $request->input('harga_masuk_weekday_wni'),
            'harga_kemah' => $request->input('harga_kemah_wni'),
            'harga_traking' => $request->input('harga_tracking_wni'),
            'harga_ansuransi' => $request->input('harga_asuransi_wni'),
        ]);
        // step 4: input price tiket wna
        ModelTiket::create([
            'id_paket_tiket' => $paketTiket->id,
            'kategori_pendaki' => 'WNA',
            'harga_masuk_wk' => $request->input('harga_masuk_weekend_wna'),
            'harga_masuk_wd' => $request->input('harga_masuk_weekday_wna'),
            'harga_kemah' => $request->input('harga_kemah_wna'),
            'harga_traking' => $request->input('harga_tracking_wna'),
            'harga_ansuransi' => $request->input('harga_asuransi_wna'),
        ]);

        return back()->with('success', 'Berhasil Create tiket');
    }

    public function update($id)
    {
        // return $id;
        $tiket = ModelPaketTiket::with('tiket_pendaki')->find($id);
        $destinasi = ModelDestinasi::find($tiket->id_destinasi);
        // $destinasi 

        // return $tiket;
        return view('etiket.admin.destinasi.tiket.tiketUpdate', [
            "tiket" => $tiket,
            "destinasi" => $destinasi,
        ]);
    }

    public function uppdateAction(Request $request)
    {
        $validateData = $request->validate([
            'id_tiket' => 'required|integer',
            'destinasi' => 'required|string|max:255',
            'nama_paket' => 'required|string|max:255',
            'min_pendaki' => 'required|integer|min:1',
            'title_penugasan' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',

            // Tiket WNI
            'id_tiket_wni' => 'required|integer',
            'harga_masuk_weekend_wni' => 'required|numeric|min:0',
            'harga_masuk_weekday_wni' => 'required|numeric|min:0',
            'harga_kemah_wni' => 'required|numeric|min:0',
            'harga_tracking_wni' => 'required|numeric|min:0',
            'harga_asuransi_wni' => 'required|numeric|min:0',
            'masa_asuransi_wni' => 'required|numeric|min:0',

            // Tiket WNA
            'id_tiket_wna' => 'required|integer',
            'harga_masuk_weekend_wna' => 'required|numeric|min:0',
            'harga_masuk_weekday_wna' => 'required|numeric|min:0',
            'harga_kemah_wna' => 'required|numeric|min:0',
            'harga_tracking_wna' => 'required|numeric|min:0',
            'harga_asuransi_wna' => 'required|numeric|min:0',
            'masa_asuransi_wna' => 'required|numeric|min:0',
        ]);

        $paket = ModelPaketTiket::findOrFail($validateData['id_tiket']);
        $paket->update([
            'nama' => $validateData['nama_paket'],
            'min_pendaki' => $validateData['min_pendaki'],
            'penugasan' => $validateData['title_penugasan'],
            'keterangan' => $validateData['keterangan'],
        ]);
        $tiketWNI = ModelTiket::findOrFail($validateData['id_tiket_wni']);
        $tiketWNI->update([
            'harga_masuk_wk' => $validateData['harga_masuk_weekend_wni'],
            'harga_masuk_wd' => $validateData['harga_masuk_weekday_wni'],
            'harga_kemah' => $validateData['harga_kemah_wni'],
            'harga_traking' => $validateData['harga_tracking_wni'],
            'harga_ansuransi' => $validateData['harga_asuransi_wni'],
            'masa_asuransi' => $validateData['masa_asuransi_wni'],
        ]);
        $tiketWNA = ModelTiket::findOrFail($validateData['id_tiket_wna']);
        $tiketWNA->update([
            'harga_masuk_wk' => $validateData['harga_masuk_weekend_wna'],
            'harga_masuk_wd' => $validateData['harga_masuk_weekday_wna'],
            'harga_kemah' => $validateData['harga_kemah_wna'],
            'harga_traking' => $validateData['harga_tracking_wna'],
            'harga_ansuransi' => $validateData['harga_asuransi_wna'],
            'masa_asuransi' => $validateData['masa_asuransi_wna'],
        ]);
        return back()->with('success', 'Berhasil Update tiket');
    }
    public function deleteAction(Request $request)
    {
        $validateData = $request->validate([
            'id_tiket' => 'required',
        ]);
        // ambil data tiket
        $tiket = ModelPaketTiket::with('tiket_pendaki')->find($validateData['id_tiket']);
        if ($tiket) {
            // delete tike wni
            ModelTiket::where('id', $tiket->tiket_pendaki[0]->id)->delete();
            // delete tike wna
            ModelTiket::where('id', $tiket->tiket_pendaki[1]->id)->delete();
            // delete paket tiket
            $tiket->delete();

            return back()->with('success', 'Berhasil Hapus tiket');
        } else {
            return back()->with('error', 'Tiket tidak ditemukan');
        }
    }
}
