<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\helper\ApiResponse;
use App\Models\GkTracking;
use App\Models\gk_pendaki;
use App\Events\TrackingUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TrackingController extends Controller
{
    /**
     * Upload single GPS position.
     * POST /api/tracking/gps
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'altitude' => 'nullable|numeric',
            'accuracy' => 'nullable|numeric|min:0',
            'battery_level' => 'nullable|integer|between:0,100',
            'recorded_at' => 'nullable|date',
        ]);

        $user = $request->user();
        $pendaki = $this->getActivePendaki($user);

        if (!$pendaki) {
            return ApiResponse::error('Tidak ada pendakian aktif', null, 403);
        }

        // Rate limit: max 1 per 30 seconds per pendaki
        $lastTrack = GkTracking::where('id_pendaki', $pendaki->id)
            ->latest('created_at')
            ->first();

        if ($lastTrack && $lastTrack->created_at->diffInSeconds(now()) < 30) {
            return ApiResponse::error('Terlalu cepat, tunggu 30 detik', null, 429);
        }

        $tracking = GkTracking::create([
            'id_pendaki' => $pendaki->id,
            'id_booking' => $pendaki->booking_id,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'altitude' => $validated['altitude'] ?? null,
            'accuracy' => $validated['accuracy'] ?? null,
            'battery_level' => $validated['battery_level'] ?? null,
            'recorded_at' => $validated['recorded_at'] ?? now(),
        ]);

        // Broadcast to admin tracking channel
        $destinasiId = $pendaki->booking->destinasi?->id;
        if ($destinasiId) {
            broadcast(new TrackingUpdated(
                destinasiId: $destinasiId,
                pendakiId: $pendaki->id,
                latitude: (float) $validated['latitude'],
                longitude: (float) $validated['longitude'],
                altitude: isset($validated['altitude']) ? (float) $validated['altitude'] : null,
                hikerName: $pendaki->fullName ?? 'Unknown',
            ));
        }

        return ApiResponse::success([
            'id' => $tracking->id,
            'server_time' => now()->toISOString(),
        ], 'GPS berhasil direkam', 201);
    }

    /**
     * Upload batch GPS positions (offline sync).
     * POST /api/tracking/gps/batch
     */
    public function storeBatch(Request $request)
    {
        $validated = $request->validate([
            'positions' => 'required|array|min:1|max:100',
            'positions.*.latitude' => 'required|numeric|between:-90,90',
            'positions.*.longitude' => 'required|numeric|between:-180,180',
            'positions.*.altitude' => 'nullable|numeric',
            'positions.*.accuracy' => 'nullable|numeric|min:0',
            'positions.*.battery_level' => 'nullable|integer|between:0,100',
            'positions.*.recorded_at' => 'required|date',
        ]);

        $user = $request->user();
        $pendaki = $this->getActivePendaki($user);

        if (!$pendaki) {
            return ApiResponse::error('Tidak ada pendakian aktif', null, 403);
        }

        $inserted = 0;
        foreach ($validated['positions'] as $pos) {
            GkTracking::create([
                'id_pendaki' => $pendaki->id,
                'id_booking' => $pendaki->booking_id,
                'latitude' => $pos['latitude'],
                'longitude' => $pos['longitude'],
                'altitude' => $pos['altitude'] ?? null,
                'accuracy' => $pos['accuracy'] ?? null,
                'battery_level' => $pos['battery_level'] ?? null,
                'recorded_at' => $pos['recorded_at'],
            ]);
            $inserted++;
        }

        return ApiResponse::success([
            'inserted' => $inserted,
            'server_time' => now()->toISOString(),
        ], "Batch GPS berhasil direkam ({$inserted} posisi)", 201);
    }

    /**
     * Get my last recorded position.
     * GET /api/tracking/my-position
     */
    public function myPosition(Request $request)
    {
        $user = $request->user();
        $pendaki = $this->getActivePendaki($user);

        if (!$pendaki) {
            return ApiResponse::error('Tidak ada pendakian aktif', null, 403);
        }

        $lastPosition = GkTracking::where('id_pendaki', $pendaki->id)
            ->latest('recorded_at')
            ->first();

        if (!$lastPosition) {
            return ApiResponse::success(null, 'Belum ada posisi terekam');
        }

        return ApiResponse::success([
            'latitude' => $lastPosition->latitude,
            'longitude' => $lastPosition->longitude,
            'altitude' => $lastPosition->altitude,
            'accuracy' => $lastPosition->accuracy,
            'battery_level' => $lastPosition->battery_level,
            'recorded_at' => $lastPosition->recorded_at->toISOString(),
        ]);
    }

    /**
     * Get active pendaki for current user (status_booking = 6 / checked-in).
     */
    private function getActivePendaki($user): ?gk_pendaki
    {
        return gk_pendaki::whereHas('booking', function ($q) use ($user) {
            $q->where('id_user', $user->id)->where('status_booking', 6);
        })->first();
    }
}
