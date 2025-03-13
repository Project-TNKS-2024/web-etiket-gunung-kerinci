<?php

namespace App\Http\Controllers\homepage;

use App\Http\Controllers\Controller;
use App\Http\Controllers\helper\homepageHelperController;
use App\Models\destinasi;
use App\Models\gk_booking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HomepageController extends Controller
{
    public function Beranda()
    {
        // ambil data cuaca 
        $homeHelper = new homepageHelperController();
        $weatherData = $homeHelper->get_cuaca();

        $destinasi = destinasi::all();

        $pendaki = $homeHelper->getPendaki();

        // return $pendaki;

        return view('homepage.beranda', [
            'destinasi' => $destinasi,

            'total_mendaki' => $pendaki['total_sedang_cekin'],
            'total_pendaki' => $pendaki['total_pendaki'],
            'total_pendaki_wna' => $pendaki['total_pendaki_wna'],
            'total_pendaki_wni' => $pendaki['total_pendaki_wni'],

            'weatherData' => $weatherData,
        ]);
    }

    public function destinasi()
    {
        $destinasi = destinasi::where('status', '2')->where('kategori', 'gunung')->with('gambar_destinasi')->get();
        return view('homepage.booking.bookingDestinasiList', [
            'destinasi' => $destinasi,
        ]);
    }

    public function gunungApi()
    {
        return view('homepage.statusGunungApi');
    }

    public function panduan()
    {
        return view('homepage.panduan');
    }
    public function faq()
    {
        return view('homepage.faq');
    }
    public function snk()
    {
        return view('homepage.snk');
    }
}
