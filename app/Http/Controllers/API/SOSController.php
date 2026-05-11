<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\helper\ApiResponse;
use App\Models\GkSos;
use App\Models\GkTracking;
use App\Models\gk_pendaki;
use App\Models\setting;
use App\Events\SOSTriggered;
use Illuminate\Http\Request;

class SOSController extends Controller
{
    /**
     * Trigger SOS panic button.
     * POST /api/sos/trigger
     */
    public function trigger(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'severity' => 'required|in:low,medium,high',
            'message' => 'nullable|string|max:1000',
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

        // Cooldown: max 1 SOS per 5 minutes
        $recentSos = GkSos::where('id_pendaki', $pendaki->id)
            ->where('created_at', '>=', now()->subMinutes(5))
            ->first();

        if ($recentSos) {
            $waitSeconds = 300 - now()->diffInSeconds($recentSos->created_at);
            return ApiResponse::error(
                "Tunggu {$waitSeconds} detik sebelum mengirim SOS lagi",
                ['wait_seconds' => $waitSeconds],
                429
            );
        }

        $sos = GkSos::create([
            'id_pendaki' => $pendaki->id,
            'id_booking' => $pendaki->booking_id,
            'id_destinasi' => $destinasiId,
            'severity' => $validated['severity'],
            'message' => $validated['message'] ?? null,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'altitude' => GkTracking::where('id_pendaki', $pendaki->id)
                ->latest('recorded_at')->value('altitude'),
            'status' => 'active',
        ]);

        broadcast(new SOSTriggered(
            destinasiId: $destinasiId,
            sosId: $sos->id,
            severity: $sos->severity,
            latitude: (float) $sos->latitude,
            longitude: (float) $sos->longitude,
            hikerName: $pendaki->fullName ?? 'Unknown',
            message: $sos->message,
        ));

        return ApiResponse::success([
            'sos_id' => $sos->id,
            'status' => 'active',
            'created_at' => $sos->created_at->toISOString(),
            'rescue_contact' => $this->getRescueContact(),
        ], 'SOS berhasil dikirim. Bantuan sedang dalam perjalanan.', 201);
    }

    /**
     * Get hiker's active SOS.
     * GET /api/sos/active
     */
    public function active(Request $request)
    {
        $user = $request->user();
        $pendaki = $this->getActivePendaki($user);

        if (!$pendaki) {
            return ApiResponse::error('Tidak ada pendakian aktif', null, 403);
        }

        $sos = GkSos::where('id_pendaki', $pendaki->id)
            ->whereIn('status', ['active', 'acknowledged', 'dispatched'])
            ->latest()
            ->first();

        if (!$sos) {
            return ApiResponse::success(null, 'Tidak ada SOS aktif');
        }

        return ApiResponse::success([
            'id' => $sos->id,
            'severity' => $sos->severity,
            'message' => $sos->message,
            'status' => $sos->status,
            'latitude' => $sos->latitude,
            'longitude' => $sos->longitude,
            'created_at' => $sos->created_at->toISOString(),
        ]);
    }

    private function getActivePendaki($user): ?gk_pendaki
    {
        return gk_pendaki::whereHas('booking', function ($q) use ($user) {
            $q->where('id_user', $user->id)->where('status_booking', 6);
        })->first();
    }

    private function getRescueContact(): ?array
    {
        $setting = setting::where('nama', 'rescue_phone')->first();
        if (!$setting) return null;

        $phone = $setting->text1;
        return [
            'phone' => $phone,
            'whatsapp_link' => 'https://wa.me/' . preg_replace('/[^0-9]/', '', $phone),
        ];
    }
}
