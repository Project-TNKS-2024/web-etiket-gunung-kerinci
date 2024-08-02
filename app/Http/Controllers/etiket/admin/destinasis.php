<?php

namespace App\Http\Controllers\etiket\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\tiket as Tiket;
use App\Models\destinasi;
use App\Models\gk_gates;
use Illuminate\Support\Facades\DB;

class destinasis extends Controller
{
    //

    public function daftar() {

        $data = Destinasi::get();

        $jenisTiket = ['Weekday','Weekend'];
        $totalTerjual = 122;

        return view('etiket.admin.master-data.destinasi', [
            "destinasi" => $data,
            "totalTerjual" => $totalTerjual,
            "jenisTiket" => $jenisTiket,
        ]);
    }

    public function tambah() {
        $destinasi = Destinasi::all();
        $gates = gk_gates::all();
        $jenisTiket = ['Weekday','Weekend'];

        return view('etiket.admin.master-data.destinasi.tambah', [
            "destinasi" => $destinasi,
            "gate" => $gates,
            "jenisTiket" => $jenisTiket,
        ]);

    }

    public function tambahAction(Request $request) {
        $request->validate([
            'nama' => 'required',
            'detail' => 'required',
        ]);

        $proceed = destinasi::create([
            "nama" => $request->nama,
            "detail" => $request->detail,
            "status" => 1,
        ]);

        if (!$proceed) {
            return back()->with('error', 'Terjadi kesalahan ketika menambahkan destinasi');
        }


        return back()->with('success', 'Berhasil menambah destinasi');
    }

    public function edit($id) {
        $data = Destinasi::where('id',$id)->first();
        $gates = gk_gates::with(['destinasi'])->where('gk_gates.id_destinasi', $id)->get();

        return view('etiket.admin.master-data.destinasi.edit', [
            'data' => $data,
            'gates' => $gates,
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
