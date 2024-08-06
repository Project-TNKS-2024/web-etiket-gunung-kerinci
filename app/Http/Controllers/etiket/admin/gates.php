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

    public function daftar()
    {

        $data = gk_gates::with(['destinasi'])->get();

        $jenisTiket = ['Weekday', 'Weekend'];
        $totalTerjual = 122;

        // return $data;

        return view('etiket.admin.master-data.gate', [
            "gates" => $data,
            "totalTerjual" => $totalTerjual,
            "jenisTiket" => $jenisTiket,
        ]);
    }

    public function tambah()
    {
        $destinasi = Destinasi::all();

        return view('etiket.admin.master-data.gate.tambah', [
            "destinasi" => $destinasi,
        ]);
    }

    public function tambahAction(Request $request)
    {

        $request->validate([
            'nama' => 'required|string|max:255',
            'status' => 'required',
            'id_destinasi' => 'required|integer',
            'max_pendaki_hari' => 'required|integer|min:0',
            'min_pendaki_booking' => 'required|integer|min:0',
            'lokasi' => 'string|max:255',
            // 'lokasi_maps' => 'string|max:255',
            'detail' => 'string',
        ]);

        // return $request;


        $proceed = gk_gates::create([
            'nama' => $request->nama,
            'status' => $request->status,
            'id_destinasi' => intval($request->id_destinasi),
            'max_pendaki_hari' => $request->max_pendaki_hari,
            'min_pendaki_booking' => $request->min_pendaki_booking,
            'lokasi' => $request->lokasi,
            'lokasi_maps' => "",
            'detail' => $request->detail,
        ]);

        if (!$proceed) {
            return back()->with('error', 'Terjadi kesalahan ketika menambahkan gate');
        }


        return back()->with('success', 'Berhasil menambah gate');
    }

    public function edit($id)
    {

        $destinasi = Destinasi::all();
        $data = gk_gates::where('id', $id)->first();

        return view('etiket.admin.master-data.gate.edit', [
            'data' => $data,
            "destinasi" => $destinasi
        ]);
    }

    public function editAction(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'status' => 'required',
            'id_destinasi' => 'required|integer',
            'max_pendaki_hari' => 'required|integer|min:0',
            'min_pendaki_booking' => 'required|integer|min:0',
            'lokasi' => 'string|max:255',
            // 'lokasi_maps' => 'string|max:255',
            'detail' => 'string',
        ]);

        // return $request;

        $proceed = gk_gates::where('id', $id)->update([
            'nama' => $request->nama,
            'status' => $request->status,
            'id_destinasi' => intval($request->id_destinasi),
            'max_pendaki_hari' => $request->max_pendaki_hari,
            'min_pendaki_booking' => $request->min_pendaki_booking,
            'lokasi' => $request->lokasi,
            'lokasi_maps' => "",
            'detail' => $request->detail,
        ]);

        if (!$proceed) {
            return back()->with('error', 'Terjadi kesalahan ketika menambahkan gate');
        }

        return back()->with('success', 'Berhasil memperbarui Gates');
    }

    public function hapus(Request $request, $id)
    {

        gk_gates::where('id', $id)->delete();
        return back()->with('success', 'Berhasil Menghapus Tiket');
    }
}
