<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\helper\ApiResponse;
use Illuminate\Support\Facades\Cache;

class DomisiliController extends Controller
{
    public $negara;
    public $provinsi;
    public $kabupaten;
    public $kecamatan;
    public $kelurahan;

    public function __construct()
    {
        $this->negara = Cache::rememberForever('Negara_data', function () {
            return json_decode(file_get_contents(public_path('assets/json/negara.json')), true);
        });
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
    // get list negara
    public function getNegara()
    {
        $data = $this->negara;
        if ($data) {
            return ApiResponse::success($data, "Berhasil", 200);
        } else {
            return ApiResponse::error('gagal mengambil data negara', '', 500);
        }
    }
    // get list provinsi
    public function getProvinsi()
    {
        $data = $this->provinsi;
        if ($data) {
            return ApiResponse::success($data, "Berhasil", 200);
        } else {
            return ApiResponse::error('gagal mengambil data provinsi', '', 500);
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
            return ApiResponse::success($data, "Berhasil", 200);
        } else {
            return ApiResponse::error('gagal mengambil data kabupaten', '', 500);
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
            return ApiResponse::success($data, "Berhasil", 200);
        } else {
            return ApiResponse::error('gagal mengambil data kecamatan', '', 500);
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
            return ApiResponse::success($data, "Berhasil", 200);
        } else {
            return ApiResponse::error('gagal mengambil data desa', '', 500);
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
            return ApiResponse::success($data, "Berhasil", 200);
        } else {
            return ApiResponse::error('gagal mengambil data provinsi', '', 500);
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
            return ApiResponse::success($data, "Berhasil", 200);
        } else {
            return ApiResponse::error('gagal mengambil data kabupaten', '', 500);
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
            return ApiResponse::success($data, "Berhasil", 200);
        } else {
            return ApiResponse::error('gagal mengambil data kecamatan', '', 500);
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
            return ApiResponse::success($data, "Berhasil", 200);
        } else {
            return ApiResponse::error('gagal mengambil data desa', '', 500);
        }
    }
}
