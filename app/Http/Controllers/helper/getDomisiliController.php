<?php

namespace App\Http\Controllers\helper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\d_Provinsi;
use App\Models\d_Kabupaten;
use App\Models\d_Kecamatan;
use App\Models\d_Kelurahan;

class getDomisiliController extends Controller
{
    // get list profinsi
    public function getProvinsi()
    {
        $data = d_Provinsi::all();
        return response()->json([
            'status' => 200,
            'message' => 'berhasil',
            'data' => $data
        ]);
    }
    // get list kabupaten by id provinsi
    public function getKabupaten($id)
    {
        $data = d_Kabupaten::where('provinsi_id', $id)->get();
        return response()->json([
            'status' => 200,
            'message' => 'berhasil',
            'data' => $data
        ]);
    }
    // get list kecamatan by id kabupaten
    public function getKecamatan($id)
    {
        $data = d_Kecamatan::where('kabupaten_id', $id)->get();
        return response()->json([
            'status' => 200,
            'message' => 'berhasil',
            'data' => $data
        ]);
    }
    // get list desa by id kecamatan
    public function getKelurahan($id)
    {
        $data = d_Kelurahan::where('kecamatan_id', $id)->get();
        return response()->json([
            'status' => 200,
            'message' => 'berhasil',
            'data' => $data
        ]);
    }

    // get detail provinsi by id
    public function getProvinsiById($id)
    {
        $data = d_Provinsi::where('id', $id)->first();
        return response()->json([
            'status' => 200,
            'message' => 'berhasil',
            'data' => $data
        ]);
    }
    // get detail kabupaten by id  
    public function getKabupatenById($id)
    {
        $data = d_Kabupaten::where('id', $id)->first();
        return response()->json([
            'status' => 200,
            'message' => 'berhasil',
            'data' => $data
        ]);
    }
    // get detail kecamatan by id
    public function getKecamatanById($id)
    {
        $data = d_Kecamatan::where('id', $id)->first();
        return response()->json([
            'status' => 200,
            'message' => 'berhasil',
            'data' => $data
        ]);
    }
    // get detail desa by id
    public function getKelurahanById($id)
    {
        $data = d_Kelurahan::where('id', $id)->first();
        return response()->json([
            'status' => 200,
            'message' => 'berhasil',
            'data' => $data
        ]);
    }
}
