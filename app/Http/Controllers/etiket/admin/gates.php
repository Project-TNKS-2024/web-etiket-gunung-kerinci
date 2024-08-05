<?php

namespace App\Http\Controllers\etiket\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\gk_tikets;
use App\Models\destinasi as Destinasi;
use App\Models\gk_gates;
use App\Models\gambar_gates;
use Illuminate\Support\Facades\DB;

class gates extends Controller
{
    //

    public function daftar() {

        $data = gk_gates::with(['destinasi'])->get();

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

        return view('etiket.admin.master-data.gate.tambah', [
            "destinasi" => $destinasi,
            "gate" => $gates,
        ]);

    }

    public function tambahAction(Request $request) {
        $request->validate([
            'nama' => 'required',
            'detail' => 'required',
            'destinasi' => 'required',
        ]);

        $id = time();
        $proceed = gk_gates::create([
            "nama" => $request->nama,
            "detail" => $request->detail,
            "lokasi" => "Lokasi Gate",
            "foto" => "-",
            "id_destinasi" => intval($request->destinasi),
        ]);

        if (!$proceed) {
            return back()->with('error', 'Terjadi kesalahan ketika menambahkan gate');
        }


        return back()->with('success', 'Berhasil menambah gate');
    }

    public function edit($id) {

        $destinasi = Destinasi::all();
        $data = gk_gates::where('id', $id)->first();

        return view('etiket.admin.master-data.gate.edit', [
            'data' => $data,
            "destinasi" => $destinasi
        ]);
    }

    public function editAction(Request $request, $id) {
        $request->validate([
            'nama' => 'required',
            'detail' => 'required',
            'destinasi' => 'required',
            'status' => 'required',
        ]);

        $proceed = gk_gates::where('id',$id)->update([
            "nama" => $request->nama,
            "detail" => $request->detail,
            "id_destinasi" => intval($request->destinasi),
            "status" => $request->status,
        ]);

        if (!$proceed) {
            return back()->with('error', 'Terjadi kesalahan ketika menambahkan gate');
        }

        return back()->with('success', 'Berhasil memperbarui tiket');

    }

    public function hapus(Request $request, $id) {

        gk_gates::where('id', $id)->delete();
        return back()->with('success', 'Berhasil Menghapus Tiket');
    }
}
