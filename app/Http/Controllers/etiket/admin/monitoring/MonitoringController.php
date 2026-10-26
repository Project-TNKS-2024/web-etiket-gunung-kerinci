<?php

namespace App\Http\Controllers\etiket\admin\monitoring;

use App\Http\Controllers\Controller;
use App\Models\GkTracking;
use App\Models\GkSos;
use App\Models\GkEmergencyMessage;
use App\Models\gk_booking;
use App\Models\GkPost;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    /**
     * Live monitoring dashboard.
     * GET /admin/monitoring
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $destinasiIds = $user->destinasis()->pluck('destinasis.id');

        // Active hikers (status_booking = 6) at admin's destinasis
        $activeBookings = gk_booking::where('status_booking', 6)
            ->whereHas('gktiket', fn($q) => $q->whereIn('id_destinasi', $destinasiIds))
            ->with(['pendakis.biodata', 'gateMasuk', 'destinasi'])
            ->get();

        // Get last known position for each active pendaki
        $pendakiIds = $activeBookings->flatMap(fn($b) => $b->pendakis->pluck('id'));
        $lastPositions = GkTracking::whereIn('id_pendaki', $pendakiIds)
            ->select('id_pendaki', 'latitude', 'longitude', 'altitude', 'battery_level', 'recorded_at')
            ->where('id', function ($q) {
                $q->select('latest.id')
                    ->from('gk_tracking as latest')
                    ->whereColumn('latest.id_pendaki', 'gk_tracking.id_pendaki')
                    ->where('latest.recorded_at', '<=', now())
                    ->orderByDesc('latest.recorded_at')
                    ->orderByDesc('latest.id')
                    ->limit(1);
            })
            ->get()
            ->keyBy('id_pendaki');

        // Active SOS
        $activeSos = GkSos::whereIn('id_destinasi', $destinasiIds)
            ->whereIn('status', ['active', 'acknowledged', 'dispatched'])
            ->with('pendaki.biodata')
            ->get();

        // Active emergencies
        $activeEmergencies = GkEmergencyMessage::whereIn('id_destinasi', $destinasiIds)
            ->where('status', 'active')
            ->count();

        // Posts for map
        $posts = GkPost::whereHas('gate', fn($q) => $q->whereIn('id_destinasi', $destinasiIds))
            ->where('status', true)
            ->get();

        $snapshot = [
            'server_time' => now()->toIso8601String(),
            'emergency_count' => $activeEmergencies,
            'hikers' => $activeBookings->flatMap(function ($booking) use ($lastPositions, $activeSos) {
                return $booking->pendakis->map(function ($pendaki) use ($booking, $lastPositions, $activeSos) {
                    $position = $lastPositions->get($pendaki->id);
                    return [
                        'id' => $pendaki->id,
                        'name' => $pendaki->fullName ?? 'Pendaki',
                        'gate' => $booking->gateMasuk?->nama ?? '',
                        'has_sos' => $activeSos->contains('id_pendaki', $pendaki->id),
                        'position' => $position ? [
                            'latitude' => (float) $position->latitude,
                            'longitude' => (float) $position->longitude,
                            'battery_level' => $position->battery_level,
                            'recorded_at' => $position->recorded_at->toIso8601String(),
                        ] : null,
                    ];
                });
            })->values(),
            'sos' => $activeSos->map(fn ($sos) => [
                'id' => $sos->id,
                'name' => $sos->pendaki?->fullName ?? 'Pendaki',
                'severity' => $sos->severity,
                'latitude' => (float) $sos->latitude,
                'longitude' => (float) $sos->longitude,
                'url' => route('admin.sos.detail', $sos->id),
            ])->values(),
            'posts' => $posts->map(fn ($post) => [
                'id' => $post->id, 'name' => $post->nama,
                'latitude' => (float) $post->latitude, 'longitude' => (float) $post->longitude,
                'radius' => (float) $post->radius_meter,
            ])->values(),
        ];

        if ($request->expectsJson()) {
            return response()->json($snapshot)->header('Cache-Control', 'no-store, private');
        }

        return view('etiket.admin.monitoring.index', compact('snapshot'));
    }
}
