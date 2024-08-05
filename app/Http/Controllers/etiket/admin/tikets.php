<?php

namespace App\Http\Controllers\etiket\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\destinasi as Destinasi;
use App\Models\gk_gates as Gates;
use App\Models\gk_paket_tiket;
use App\Models\gk_tiket_pendaki;
use Illuminate\Support\Facades\DB;

class tikets extends Controller
{
    //

    public function daftar()
    {

]
        // $data = gk_tikets::with(['destinasi', 'gk_gate', 'kategori', 'golongan'])->get();
        $data = gk_tiket_pendaki::with('paketTiket')->get();

        return $data;


        $jenisTiket = ['Weekday', 'Weekend'];
        $totalTerjual = 122;

        return view('etiket.admin.master-data.tiket', [
            "tiket" => $data,
            "totalTerjual" => $totalTerjual,
            "jenisTiket" => $jenisTiket,
        ]);
    }

    public function tambah()
    {
        $destinasi = Destinasi::all();
        $gates = Gates::all();
        $jenisTiket = ['Weekday', 'Weekend'];

        return view('etiket.admin.master-data.tiket.tambah', [
            "destinasi" => $destinasi,
            "gate" => $gates,
            "jenisTiket" => $jenisTiket,
        ]);
    }

    public function tambahAction(Request $request)
    {
        $request->validate([
            'destinasi' => 'required',
            'tipe' => 'required', // Sesuaikan dengan kebutuhan Anda
            'jenis' => 'required',
            'gate' => 'required',
            'hargaTiket' => 'required',
        ]);

        DB::table('tikets')->insert([
            'id_destinasi' => $request->destinasi,
            'nama' => $request->tipe,
            'spesial' => 'gunungKerinci',
            'keterangan' => '-',
            'harga wna' => 0, // Fixed key
            'harga wni' => 0, // Fixed key
            'gate' => 1, // Ensure this field is correctly passed
            'jenisTiket' => $request->jenis,
            'harga' => $request->hargaTiket,
        ]);


        return back()->with('success', 'Berhasil menambah tiket');
    }

    public function edit($id)
    {

        $destinasi = Destinasi::all();
        $gates = Gates::all();
        $jenisTiket = ['Weekday', 'Weekend'];


        $data = gk_paket_tiket::with(['destinasi', 'tiket_pendaki'])->where('gk_paket_tikets.id', $id)->first();
        $paket_tiket = gk_paket_tiket::all();


        return view('etiket.admin.master-data.tiket.edit', [
            'data' => $data,
            "destinasi" => $destinasi,
            "gate" => $gates,
            "jenisTiket" => $jenisTiket,
            "paket_tiket" => $paket_tiket,
        ]);
    }

    public function editAction(Request $request, $id)
    {
        $request->validate([
            'destinasi' => 'required',
            'tipe' => 'required', // Sesuaikan dengan kebutuhan Anda
            'jenis' => 'required',
            'gate' => 'required',
            'hargaTiket' => 'required',
        ]);

        DB::table('tikets')->where('id', $id)->update([
            'id_destinasi' => $request->destinasi,
            'nama' => $request->tipe,
            'spesial' => 'gunungKerinci',
            'keterangan' => '-',
            'harga wna' => 0, // Fixed key
            'harga wni' => 0, // Fixed key
            'gate' => 1, // Ensure this field is correctly passed
            'jenisTiket' => $request->jenis,
            'harga' => $request->hargaTiket,
        ]);


        return back()->with('success', 'Berhasil memperbarui tiket');
    }

    public function hapus(Request $reqeust, $id)
    {

        gk_tikets::where('id', $id)->delete();
        return back()->with('success', 'Berhasil Menghapus Tiket');
    }
}
