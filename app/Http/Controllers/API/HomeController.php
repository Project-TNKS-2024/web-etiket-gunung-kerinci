<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\helper\ApiResponse;
use App\Http\Controllers\helper\homepageHelperController;
use App\Models\gk_paket_tiket;
use App\Models\destinasi;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Menampilkan data beranda dengan cuaca, tiket aktif, dan destinasi
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function beranda(Request $request)
    {
        // cuaca kerinci hari ini
        $homeHelper = new homepageHelperController();
        $cuaca = $homeHelper->get_cuaca();

        // tiket aktif hari ini
        $tiketAktif = gk_paket_tiket::with(['tiket_pendaki', 'destinasi'])
            ->get();

        // list destinasi wisata
        $destinasi = destinasi::where('status', 1)
            ->with('gambar_destinasi')
            ->get();

        $data = [
            'cuaca' => $cuaca,
            'tiket_aktif' => $tiketAktif,
            'destinasi' => $destinasi,
        ];

        return ApiResponse::success($data, 'Berhasil mengubah data', 200);
    }
}
