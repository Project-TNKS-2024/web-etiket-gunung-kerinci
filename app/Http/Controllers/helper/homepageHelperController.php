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
        $key = config('services.weather.key');
        if (!$key) {
            return null;
        }
        try {
            return \Illuminate\Support\Facades\Cache::remember('weather.kerinci.id.v1.'.sha1(config('services.weather.coordinates')), 300, function () use ($key) {
                $response = \Illuminate\Support\Facades\Http::connectTimeout(2)->timeout(5)
                    ->withOptions(['verify' => config('services.weather.ca_bundle') ?: true])
                    ->get('https://api.weatherapi.com/v1/current.json', [
                        'key' => $key,
                        'q' => config('services.weather.coordinates'),
                        'aqi' => 'no',
                        'lang' => 'id',
                    ]);
                if (!$response->successful()) {
                    Log::warning('Data cuaca tidak tersedia.', ['http_status' => $response->status()]);
                    return null;
                }
                $data = $response->json();
                return is_array($data) && isset($data['current']['temp_c']) ? $data : null;
            });
        } catch (\Throwable $e) {
            // Exception HTTP dapat memuat URL/key, jangan sertakan detail request.
            Log::warning('Koneksi layanan cuaca tidak tersedia.');
            return null;
        }
    }
    public function getPendaki(?int $destinationId = null)
    {
        $destinationId ??= \App\Models\destinasi::where('nama', 'Gunung Kerinci')->value('id');
        // Kunjungan Kerinci yang sudah check-in; termasuk riwayat checkout.
        $PKerinci = gk_pendaki::with(['biodata', 'getStatus' => fn ($q) => $q->orderByDesc('created_at')->orderByDesc('id')])->whereHas('booking', function ($q) use ($destinationId) {
            $q->whereHas('destinasi', function ($query) use ($destinationId) {
                $query->where('destinasis.id', $destinationId);
            })->whereBetween('status_booking', [6, 8]);
        })->get();

        // Filter pendaki yang melakukan cekin
        $filteredPendaki = $PKerinci->filter(function ($pendaki) {
            return in_array((int) $pendaki->getStatus->first()?->status, [2, 3], true);
        });

        $totalPendaki = $filteredPendaki->count();
        $totalPendakiWNI = $filteredPendaki->filter(function ($pendaki) {
            return optional($pendaki->biodata)->kenegaraan === 'ID';
        })->count();

        $totalPendakiWNA = $filteredPendaki->filter(function ($pendaki) {
            return optional($pendaki->biodata)->kenegaraan !== 'ID';
        })->count();

        $totalPendakiCekin = $filteredPendaki->filter(function ($pendaki) {
            return (int) $pendaki->getStatus->first()?->status === 2;
        })->count();


        return [
            'total_pendaki' => $totalPendaki,
            'total_pendaki_wna' => $totalPendakiWNA,
            'total_pendaki_wni' => $totalPendakiWNI,
            'total_sedang_cekin' => $totalPendakiCekin,
        ];
    }
}
