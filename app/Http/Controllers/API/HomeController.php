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
        $tiketAktif = gk_paket_tiket::select('id', 'id_destinasi', 'nama', 'min_pendaki', 'penugasan', 'keterangan')
            ->with([
                'tiket_pendaki:id,id_paket_tiket,kategori_pendaki,harga_masuk_wk,harga_masuk_wd,harga_kemah,harga_traking,harga_ansuransi,masa_ansuransi',
                'destinasi:id,nama,status,statusGunung,kategori,lokasi,detail,sop',
            ])
            ->get();

        // list destinasi wisata, disesuaikan dengan beranda web
        $destinasi = destinasi::select('id', 'nama', 'status', 'statusGunung', 'kategori', 'lokasi', 'detail', 'sop')
            ->with('gambar_destinasi:id,id_destinasi,src,nama,detail')
            ->get();

        $pendaki = $homeHelper->getPendaki();

        $data = [
            'cuaca' => $cuaca,
            'total_mendaki' => $pendaki['total_sedang_cekin'],
            'total_pendaki' => $pendaki['total_pendaki'],
            'total_pendaki_wna' => $pendaki['total_pendaki_wna'],
            'total_pendaki_wni' => $pendaki['total_pendaki_wni'],
            'tiket_aktif' => $tiketAktif,
            'destinasi' => $destinasi,
        ];

        return ApiResponse::success($data, 'Berhasil mengambil data beranda', 200);
    }
}
