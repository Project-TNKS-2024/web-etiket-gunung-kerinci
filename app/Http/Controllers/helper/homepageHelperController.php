<?php

namespace App\Http\Controllers\helper;

use App\Http\Controllers\Controller;
use App\Models\gk_booking;
use App\Models\gk_pendaki;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class homepageHelperController extends Controller
{
    public function get_cuaca()
    {
        try {
            $apiWeatherKey = 'a2e80ea3991444f38a015609251802';
            $laLongitude = '-1.6955471535960556,101.26376495341809'; // gunung kerinci
            $apiWeatherUrl = 'https://api.weatherapi.com/v1/current.json?key=' . $apiWeatherKey . '&q=' . $laLongitude . '&aqi=no';
            $weatherResponse = file_get_contents($apiWeatherUrl);
            $weatherData = json_decode($weatherResponse, true);
        } catch (\Exception $e) {
            Log::channel('admin')->error(
                'Terjadi kesalahan pada proses pengambilan cuaca dari api  ',
                [
                    'api' => $apiWeatherUrl,
                    'error' => $e->getMessage()
                ]
            );
            $weatherData = null;
        }
        return $weatherData;
    }
    public function getPendaki()
    {
        // ambil semua pendaki gunung kerinci yang status booking >= 6
        $PKerinci = gk_pendaki::whereHas('booking', function ($q) {
            $q->whereHas('destinasi', function ($query) {
                $query->where('destinasis.id', 1);
            })->where('status_booking', '>=', 6);
        })->get();

        // Filter pendaki yang melakukan cekin
        $filteredPendaki = $PKerinci->filter(function ($pendaki) {
            return $pendaki->getStatus()->count() > 0 &&
                optional($pendaki->getStatus()->latest()->first())->status > 1;
        });

        $totalPendaki = $filteredPendaki->count();
        $totalPendakiWNI = $filteredPendaki->filter(function ($pendaki) {
            return optional($pendaki->biodata)->kenegaraan === 'ID';
        })->count();

        $totalPendakiWNA = $filteredPendaki->filter(function ($pendaki) {
            return optional($pendaki->biodata)->kenegaraan !== 'ID';
        })->count();

        $totalPendakiCekin = $filteredPendaki->filter(function ($pendaki) {
            return optional($pendaki->getStatus()->latest()->first())->status == 2;
        })->count();


        return [
            'total_pendaki' => $totalPendaki,
            'total_pendaki_wna' => $totalPendakiWNA,
            'total_pendaki_wni' => $totalPendakiWNI,
            'total_sedang_cekin' => $totalPendakiCekin,
        ];
    }
}
