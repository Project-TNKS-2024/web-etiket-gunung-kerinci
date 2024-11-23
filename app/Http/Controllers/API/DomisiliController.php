<?php

namespace App\Http\Controllers\helper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\d_Provinsi;
use App\Models\d_Kabupaten;
use App\Models\d_Kecamatan;
use App\Models\d_Kelurahan;

class DomisiliController extends Controller
{
    public $profinsi;
    public $kabupaten;
    public $kecamatan;
    public $kelurahan;

    public function __construct()
    {
        $this->profinsi = file_get_contents(public_path('assets/json/provinsi.json'));
        $this->profinsi = json_decode($this->profinsi, true);
        $this->kabupaten = file_get_contents(public_path('assets/json/kabupaten.json'));
        $this->kabupaten = json_decode($this->kabupaten, true);
        $this->kecamatan = file_get_contents(public_path('assets/json/kecamatan.json'));
        $this->kecamatan = json_decode($this->kecamatan, true);
        $this->kelurahan = file_get_contents(public_path('assets/json/kelurahan.json'));
        $this->kelurahan = json_decode($this->kelurahan, true);
    }
    // get list profinsi
    public function getProvinsi()
    {
        $data = $this->profinsi;
        if ($data) {
            return response()->json([
                'status' => 200,
                'message' => 'berhasil',
                'data' => $data
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'gagal mengambil data provinsi',
                'data' => $data
            ]);
        }
    }

    // get list kabupaten by id provinsi
    public function getKabupatenByIdProvinsi($id)
    {
        $data = $this->kabupaten;
        $data = array_filter($data, function ($item) use ($id) {
            return $item['provinsi_id'] == $id;
        });
        if ($data) {
            return response()->json([
                'status' => 200,
                'message' => 'berhasil',
                'data' => $data
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'gagal mengambil data kabupaten',
                'data' => $data
            ]);
        }
    }

    // get list kecamatan by id kabupaten
    public function getKecamatanByIdKabupaten($id)
    {
        $data = $this->kecamatan;
        $data = array_filter($data, function ($item) use ($id) {
            return $item['kabupaten_id'] == $id;
        });
        if ($data) {
            return response()->json([
                'status' => 200,
                'message' => 'berhasil',
                'data' => $data
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'gagal mengambil data kecamatan',
                'data' => $data
            ]);
        }
    }

    // get list desa by id kecamatan
    public function getKelurahanByIdKecamatan($id)
    {
        $data = $this->kelurahan;
        $data = array_filter($data, function ($item) use ($id) {
            return $item['kecamatan_id'] == $id;
        });
        if ($data) {
            return response()->json([
                'status' => 200,
                'message' => 'berhasil',
                'data' => $data
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'gagal mengambil data desa',
                'data' => $data
            ]);
        }
    }

    // get detail provinsi by id
    public function getProvinsiById($id)
    {
        $data = $this->profinsi;
        $data = array_filter($data, function ($item) use ($id) {
            return $item['id'] == $id;
        });
        if ($data) {
            return response()->json([
                'status' => 200,
                'message' => 'berhasil',
                'data' => $data
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'gagal mengambil data provinsi',
                'data' => $data
            ]);
        }
    }

    // get detail kabupaten by id  
    public function getKabupatenById($id)
    {
        $data = $this->kabupaten;
        $data = array_filter($data, function ($item) use ($id) {
            return $item['id'] == $id;
        });
        if ($data) {
            return response()->json([
                'status' => 200,
                'message' => 'berhasil',
                'data' => $data
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'gagal mengambil data kabupaten',
                'data' => $data
            ]);
        }
    }

    // get detail kecamatan by id
    public function getKecamatanById($id)
    {
        $data = $this->kecamatan;
        $data = array_filter($data, function ($item) use ($id) {
            return $item['id'] == $id;
        });
        if ($data) {
            return response()->json([
                'status' => 200,
                'message' => 'berhasil',
                'data' => $data
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'gagal mengambil data kecamatan',
                'data' => $data
            ]);
        }
    }

    // get detail desa by id
    public function getKelurahanById($id)
    {
        $data = $this->kelurahan;
        $data = array_filter($data, function ($item) use ($id) {
            return $item['id'] == $id;
        });
        if ($data) {
            return response()->json([
                'status' => 200,
                'message' => 'berhasil',
                'data' => $data
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'gagal mengambil data desa',
                'data' => $data
            ]);
        }
    }
}
