<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class DomisiliController extends Controller
{
    public $provinsi;
    public $kabupaten;
    public $kecamatan;
    public $kelurahan;

    public function __construct()
    {
        $this->provinsi = Cache::rememberForever('provinsi_data', function () {
            return json_decode(file_get_contents(public_path('assets/json/provinsi.json')), true);
        });

        $this->kabupaten = Cache::rememberForever('kabupaten_data', function () {
            return json_decode(file_get_contents(public_path('assets/json/kabupaten.json')), true);
        });

        $this->kecamatan = Cache::rememberForever('kecamatan_data', function () {
            return json_decode(file_get_contents(public_path('assets/json/kecamatan.json')), true);
        });

        $this->kelurahan = Cache::rememberForever('kelurahan_data', function () {
            return json_decode(file_get_contents(public_path('assets/json/kelurahan.json')), true);
        });
    }
    // get list provinsi
    public function getProvinsi()
    {
        $data = $this->provinsi;
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
        $data = $this->provinsi;
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
