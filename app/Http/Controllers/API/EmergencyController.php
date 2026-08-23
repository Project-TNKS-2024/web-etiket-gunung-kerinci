<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\helper\ApiResponse;
use App\Models\GkEmergencyMessage;
use App\Models\GkTracking;
use App\Models\gk_pendaki;
use App\Events\EmergencyTriggered;
use Illuminate\Http\Request;

class EmergencyController extends Controller
{
    /**
     * Hiker triggers emergency alert.
     * POST /api/emergency/trigger
     */
    public function trigger(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'severity' => 'required|in:low,medium,high,critical',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $user = $request->user();
        $pendaki = $this->getActivePendaki($user);

        if (!$pendaki) {
            return ApiResponse::error('Tidak ada pendakian aktif', null, 403);
        }

        $destinasiId = $pendaki->booking->destinasi?->id;
        if (!$destinasiId) {
            return ApiResponse::error('Destinasi tidak ditemukan', null, 404);
        }

        // Use provided coordinates or fall back to last known GPS
        $lat = $validated['latitude'] ?? null;
        $lon = $validated['longitude'] ?? null;

        if (!$lat || !$lon) {
            $lastPos = GkTracking::where('id_pendaki', $pendaki->id)
                ->latest('recorded_at')->first();
            if ($lastPos) {
                $lat = $lastPos->latitude;
                $lon = $lastPos->longitude;
            }
        }

        $emergency = GkEmergencyMessage::create([
            'id_user' => $user->id,
            'id_destinasi' => $destinasiId,
            'id_pendaki' => $pendaki->id,
            'id_booking' => $pendaki->booking_id,
            'type' => 'hiker_alert',
            'title' => $validated['title'],
            'description' => $validated['description'],
            'severity' => $validated['severity'],
            'latitude' => $lat,
            'longitude' => $lon,
            'status' => 'active',
        ]);

        broadcast(new EmergencyTriggered(
            destinasiId: $destinasiId,
            emergencyId: $emergency->id,
            title: $emergency->title,
            description: $emergency->description,
            severity: $emergency->severity,
            latitude: $lat ? (float) $lat : null,
            longitude: $lon ? (float) $lon : null,
            hikerName: $pendaki->fullName ?? 'Unknown',
        ));

        return ApiResponse::success([
            'id' => $emergency->id,
            'status' => $emergency->status,
            'created_at' => $emergency->created_at->toISOString(),
        ], 'Pesan darurat berhasil dikirim', 201);
    }

    /**
     * Get active emergencies for hiker's destination.
     * GET /api/emergency/active
     */
    public function active(Request $request)
    {
        $user = $request->user();
        $pendaki = $this->getActivePendaki($user);

        if (!$pendaki) {
            return ApiResponse::error('Tidak ada pendakian aktif', null, 403);
        }

        $destinasiId = $pendaki->booking->destinasi?->id;
        if (!$destinasiId) {
            return ApiResponse::success([]);
        }

        $emergencies = GkEmergencyMessage::where('id_destinasi', $destinasiId)
            ->where('status', 'active')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($e) => [
                'id' => $e->id,
                'type' => $e->type,
                'title' => $e->title,
                'description' => $e->description,
                'severity' => $e->severity,
                'latitude' => $e->latitude,
                'longitude' => $e->longitude,
                'created_at' => $e->created_at->toISOString(),
            ]);

        return ApiResponse::success($emergencies);
    }

    private function getActivePendaki($user): ?gk_pendaki
    {
        return gk_pendaki::whereHas('booking', function ($q) use ($user) {
            $q->where('id_user', $user->id)->where('status_booking', 6);
        })->first();
    }
}
