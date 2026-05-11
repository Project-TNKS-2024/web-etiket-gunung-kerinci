<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\helper\ApiResponse;
use App\Models\GkCheckpointLog;
use App\Models\GkPost;
use App\Models\gk_pendaki;
use App\Models\gk_booking;
use App\Services\GeoService;
use Illuminate\Http\Request;

class CheckpointController extends Controller
{
    /**
     * Check-in via QR code scan.
     * POST /api/tracking/checkpoint/qr
     */
    public function scanQr(Request $request)
    {
        $validated = $request->validate([
            'qr_code_value' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $user = $request->user();
        $pendaki = $this->getActivePendaki($user);
        if (!$pendaki) {
            return ApiResponse::error('Tidak ada pendakian aktif', null, 403);
        }

        $post = GkPost::where('qr_code_value', $validated['qr_code_value'])
            ->where('status', true)->first();

        if (!$post) {
            return ApiResponse::error('QR code tidak valid', null, 404);
        }

        // Prevent duplicate (same post within same booking)
        $existing = GkCheckpointLog::where('id_pendaki', $pendaki->id)
            ->where('id_post', $post->id)
            ->where('id_booking', $pendaki->booking_id)
            ->first();

        if ($existing) {
            return ApiResponse::error('Sudah check-in di pos ini', ['checked_at' => $existing->checked_at->toISOString()], 409);
        }

        $log = GkCheckpointLog::create([
            'id_pendaki' => $pendaki->id,
            'id_post' => $post->id,
            'id_booking' => $pendaki->booking_id,
            'method' => 'qr',
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'checked_at' => now(),
        ]);

        $progress = $this->getProgress($pendaki);

        return ApiResponse::success([
            'checkpoint_log_id' => $log->id,
            'post' => ['id' => $post->id, 'nama' => $post->nama, 'urutan' => $post->urutan],
            'progress' => $progress,
        ], "Check-in berhasil di {$post->nama}", 201);
    }

    /**
     * GPS proximity detection - find nearest post.
     * POST /api/tracking/checkpoint/gps
     */
    public function detectGps(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric|min:0',
        ]);

        $user = $request->user();
        $pendaki = $this->getActivePendaki($user);
        if (!$pendaki) {
            return ApiResponse::error('Tidak ada pendakian aktif', null, 403);
        }

        $gateId = $pendaki->booking->gate_masuk;
        $posts = GkPost::where('id_gate', $gateId)->where('status', true)->orderBy('urutan')->get();

        if ($posts->isEmpty()) {
            return ApiResponse::error('Tidak ada pos di jalur ini', null, 404);
        }

        $nearest = GeoService::findNearestPost(
            (float) $validated['latitude'],
            (float) $validated['longitude'],
            $posts
        );

        $allDistances = $posts->map(fn($p) => [
            'id' => $p->id,
            'nama' => $p->nama,
            'urutan' => $p->urutan,
            'distance_meters' => round(GeoService::distance(
                (float) $validated['latitude'], (float) $validated['longitude'],
                (float) $p->latitude, (float) $p->longitude
            ), 2),
            'within_radius' => GeoService::isWithinRadius(
                (float) $validated['latitude'], (float) $validated['longitude'],
                (float) $p->latitude, (float) $p->longitude,
                $p->radius_meter
            ),
        ]);

        return ApiResponse::success([
            'nearest_post' => $nearest ? [
                'id' => $nearest['post']->id,
                'nama' => $nearest['post']->nama,
                'distance_meters' => $nearest['distance'],
                'within_radius' => $nearest['within_radius'],
                'radius_meter' => $nearest['post']->radius_meter,
            ] : null,
            'all_posts' => $allDistances,
        ]);
    }

    /**
     * Manual check-in at a post.
     * POST /api/tracking/checkpoint/manual
     */
    public function manualCheckin(Request $request)
    {
        $validated = $request->validate([
            'post_id' => 'required|exists:gk_posts,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $user = $request->user();
        $pendaki = $this->getActivePendaki($user);
        if (!$pendaki) {
            return ApiResponse::error('Tidak ada pendakian aktif', null, 403);
        }

        $post = GkPost::findOrFail($validated['post_id']);

        // Check duplicate
        $existing = GkCheckpointLog::where('id_pendaki', $pendaki->id)
            ->where('id_post', $post->id)
            ->where('id_booking', $pendaki->booking_id)
            ->first();

        if ($existing) {
            return ApiResponse::error('Sudah check-in di pos ini', ['checked_at' => $existing->checked_at->toISOString()], 409);
        }

        // Check if manual override (> 500m from post)
        $distance = GeoService::distance(
            (float) $validated['latitude'], (float) $validated['longitude'],
            (float) $post->latitude, (float) $post->longitude
        );
        $isOverride = $distance > 500;

        $log = GkCheckpointLog::create([
            'id_pendaki' => $pendaki->id,
            'id_post' => $post->id,
            'id_booking' => $pendaki->booking_id,
            'method' => 'manual',
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'is_manual_override' => $isOverride,
            'checked_at' => now(),
        ]);

        $progress = $this->getProgress($pendaki);

        return ApiResponse::success([
            'checkpoint_log_id' => $log->id,
            'post' => ['id' => $post->id, 'nama' => $post->nama, 'urutan' => $post->urutan],
            'is_manual_override' => $isOverride,
            'distance_from_post' => round($distance, 2),
            'progress' => $progress,
        ], "Check-in manual berhasil di {$post->nama}", 201);
    }

    /**
     * Get trail progress for a booking.
     * GET /api/tracking/progress/{booking_id}
     */
    public function progress(Request $request, $bookingId)
    {
        $user = $request->user();
        $booking = gk_booking::where('id', $bookingId)
            ->where('id_user', $user->id)
            ->firstOrFail();

        $gateId = $booking->gate_masuk;
        $posts = GkPost::where('id_gate', $gateId)->where('status', true)->orderBy('urutan')->get();

        $pendakiIds = $booking->pendakis()->pluck('id');
        $logs = GkCheckpointLog::where('id_booking', $bookingId)
            ->whereIn('id_pendaki', $pendakiIds)
            ->get()
            ->groupBy('id_pendaki');

        $totalPosts = $posts->count();

        $hikerProgress = [];
        foreach ($booking->pendakis as $pendaki) {
            $pendakiLogs = $logs->get($pendaki->id, collect());
            $completedPosts = $pendakiLogs->pluck('id_post')->unique();

            $hikerProgress[] = [
                'pendaki_id' => $pendaki->id,
                'nama' => $pendaki->fullName,
                'completed' => $completedPosts->count(),
                'total' => $totalPosts,
                'percentage' => $totalPosts > 0 ? round(($completedPosts->count() / $totalPosts) * 100) : 0,
                'checkpoints' => $posts->map(fn($p) => [
                    'post_id' => $p->id,
                    'nama' => $p->nama,
                    'urutan' => $p->urutan,
                    'completed' => $completedPosts->contains($p->id),
                    'checked_at' => $pendakiLogs->firstWhere('id_post', $p->id)?->checked_at?->toISOString(),
                    'method' => $pendakiLogs->firstWhere('id_post', $p->id)?->method,
                ]),
            ];
        }

        return ApiResponse::success([
            'booking_id' => $bookingId,
            'gate' => $booking->gateMasuk?->nama,
            'total_posts' => $totalPosts,
            'hikers' => $hikerProgress,
        ]);
    }

    /**
     * Get all posts for a gate/route.
     * GET /api/tracking/posts/{gate_id}
     */
    public function posts($gateId)
    {
        $posts = GkPost::where('id_gate', $gateId)
            ->where('status', true)
            ->orderBy('urutan')
            ->get(['id', 'nama', 'urutan', 'latitude', 'longitude', 'altitude', 'radius_meter']);

        return ApiResponse::success($posts);
    }

    private function getActivePendaki($user): ?gk_pendaki
    {
        return gk_pendaki::whereHas('booking', function ($q) use ($user) {
            $q->where('id_user', $user->id)->where('status_booking', 6);
        })->first();
    }

    private function getProgress(gk_pendaki $pendaki): array
    {
        $gateId = $pendaki->booking->gate_masuk;
        $totalPosts = GkPost::where('id_gate', $gateId)->where('status', true)->count();
        $completed = GkCheckpointLog::where('id_pendaki', $pendaki->id)
            ->where('id_booking', $pendaki->booking_id)
            ->distinct('id_post')->count('id_post');

        return [
            'completed' => $completed,
            'total' => $totalPosts,
            'percentage' => $totalPosts > 0 ? round(($completed / $totalPosts) * 100) : 0,
        ];
    }
}
