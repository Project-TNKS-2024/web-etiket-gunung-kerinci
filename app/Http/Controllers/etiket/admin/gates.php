<?php

namespace App\Http\Controllers\etiket\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\gk_tikets;
use App\Models\destinasi as Destinasi;
use App\Models\gk_gates;
use Illuminate\Support\Facades\DB;

class gates extends Controller
{
    //

    public function daftar() {

        $data = gk_gates::all();

        $jenisTiket = ['Weekday','Weekend'];
        $totalTerjual = 122;

        return view('etiket.admin.master-data.gate', [
            "gates" => $data,
            "totalTerjual" => $totalTerjual,
            "jenisTiket" => $jenisTiket,
        ]);
    }

    public function tambah() {
        $destinasi = Destinasi::all();
        $gates = gk_gates::all();
        $jenisTiket = ['Weekday','Weekend'];

        return view('etiket.admin.master-data.gate.tambah', [
            "destinasi" => $destinasi,
            "gate" => $gates,
            "jenisTiket" => $jenisTiket,
        ]);

    }

    public function tambahAction(Request $request) {
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

    public function edit($id) {

        $destinasi = Destinasi::all();
        $gates = gk_gates::all();
        $jenisTiket = ['Weekday','Weekend'];
        $data = gk_gates::where('id', $id)->first();

        return view('etiket.admin.master-data.destinasi.edit', [
            'data' => $data,
            "destinasi" => $destinasi,
            "gate" => $gates,
            "jenisTiket" => $jenisTiket,
        ]);
    }

    public function editAction(Request $request, $id) {
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

    public function hapus(Request $reqeust, $id) {

        Tiket::where('id', $id)->delete();
        return back()->with('success', 'Berhasil Menghapus Tiket');
    }
}
