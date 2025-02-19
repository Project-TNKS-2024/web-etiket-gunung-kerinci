<?php

namespace App\Http\Controllers\homepage;

use App\Http\Controllers\Controller;
use App\Models\destinasi;
use App\Models\gk_booking;
use App\Models\gk_pendaki;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomepageController extends Controller
{
    public function Beranda()
    {
        // ambil data cuaca 
        $apiWeatherKey = 'a2e80ea3991444f38a015609251802';
        $laLongitude = '-1.6955471535960556,101.26376495341809'; // gunung kerinci
        $apiWeatherUrl = 'https://api.weatherapi.com/v1/current.json?key=' . $apiWeatherKey . '&q=' . $laLongitude . '&aqi=no';
        $weatherResponse = file_get_contents($apiWeatherUrl);
        $weatherData = json_decode($weatherResponse, true);

        $destinasi = destinasi::all();

        // ambil data pendakian gunung kerinci yang sudah cekin 
        $Bkerinci = gk_booking::whereHas('destinasi', function ($query) {
            $query->where('destinasis.id', 1);
        })->where('status_booking', '>=', 6);

        $total_pendaki_wna = $Bkerinci->sum('total_pendaki_wna');
        $total_pendaki_wni = $Bkerinci->sum('total_pendaki_wni');
        $total_pendaki = $total_pendaki_wna + $total_pendaki_wni;
        $total_sedang_cekin = (clone $Bkerinci)
            ->where(function ($query) {
                $query->Where('tanggal_keluar', date('Y-m-d'));
            })
            ->sum(DB::raw('total_pendaki_wna + total_pendaki_wni'));

        // return $weatherData->current;
        // return $weatherData;
        return view('homepage.beranda', [
            'destinasi' => $destinasi,
            'total_mendaki' => $total_sedang_cekin,
            'total_pendaki' => $total_pendaki,
            'total_pendaki_wna' => $total_pendaki_wna,
            'total_pendaki_wni' => $total_pendaki_wni,
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
