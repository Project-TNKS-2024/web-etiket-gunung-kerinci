<?php

namespace App\Http\Controllers\API;

use App\Exceptions\CheckpointException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\helper\ApiResponse;
use App\Models\gk_booking;
use App\Models\gk_pendaki;
use App\Models\GkCheckpointLog;
use App\Models\GkCheckpointMemberNote;
use App\Models\GkPost;
use App\Services\CheckpointItineraryService;
use App\Services\CheckpointVisitService;
use App\Services\GeoService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Kontrak v2 untuk checkpoint naik–turun. Endpoint v1 tetap menyajikan proyeksi
 * naik dengan makna lama; kontrak ini menambah fase dan idempotensi event.
 */
class CheckpointV2Controller extends Controller
{
    public function __construct(
        private CheckpointVisitService $visits,
        private CheckpointItineraryService $itinerary
    ) {}

    /**
     * GET /api/tracking/v2/posts/{gate_id}
     */
    public function posts($gateId)
    {
        $posts = GkPost::where('id_gate', $gateId)
            ->where('status', true)
            ->orderBy('urutan')
            ->get([
                'id', 'nama', 'urutan', 'latitude', 'longitude', 'altitude', 'radius_meter', 'detail',
                'is_summit', 'mountain_status', 'water_source_status', 'camping_status', 'temperature_c',
                'distance_to_next_m', 'estimated_time_minutes', 'estimated_descent_time_minutes',
                'distance_to_summit_m', 'info_is_dummy', 'info_note', 'info_updated_at', 'map_image_path',
            ]);

        return ApiResponse::success($posts);
    }

    /**
     * POST /api/tracking/v2/checkpoint/qr
     */
    public function scanQr(Request $request)
    {
        $validated = $request->validate([
            'qr_code_value' => 'required|string',
            'pendaki_id' => 'sometimes|nullable|uuid',
            'phase' => 'required|in:naik,turun',
            'client_event_id' => 'required|uuid',
            'recorded_at' => 'required|date',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        [$actor, $target, $error] = $this->resolveTarget($request->user(), $validated['pendaki_id'] ?? null);
        if ($error !== null) {
            return $error;
        }

        $post = GkPost::where('qr_code_value', $validated['qr_code_value'])
            ->where('status', true)->first();
        if (! $post || (int) $post->id_gate !== (int) $actor->booking->gate_masuk) {
            return ApiResponse::error('QR code tidak valid', null, 404);
        }

        try {
            $recordedAt = Carbon::parse($validated['recorded_at']);
            $result = $this->visits->record(
                $actor, $target, $post, $validated['phase'], 'qr',
                $validated['latitude'] ?? null, $validated['longitude'] ?? null,
                false, $validated['client_event_id'], $recordedAt
            );
            $data = $this->visits->checkinResponse($result['log'], $result['target'], $post, $result['idempotent']);

            return ApiResponse::success($data, "Check-in berhasil di {$post->nama}", $result['idempotent'] ? 200 : 201);
        } catch (CheckpointException $e) {
            return ApiResponse::error($e->getMessage(), $e->toErrors(), $e->httpStatus);
        }
    }

    /**
     * POST /api/tracking/v2/checkpoint/manual
     */
    public function manualCheckin(Request $request)
    {
        $validated = $request->validate([
            'post_id' => 'required|exists:gk_posts,id',
            'pendaki_id' => 'sometimes|nullable|uuid',
            'phase' => 'required|in:naik,turun',
            'client_event_id' => 'required|uuid',
            'recorded_at' => 'required|date',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        [$actor, $target, $error] = $this->resolveTarget($request->user(), $validated['pendaki_id'] ?? null);
        if ($error !== null) {
            return $error;
        }

        $post = GkPost::findOrFail($validated['post_id']);
        if ((int) $post->id_gate !== (int) $actor->booking->gate_masuk) {
            return ApiResponse::error('Pos tidak ditemukan', null, 404);
        }

        $distance = GeoService::distance(
            (float) $validated['latitude'], (float) $validated['longitude'],
            (float) $post->latitude, (float) $post->longitude
        );
        $isOverride = $distance > 500;

        try {
            $recordedAt = Carbon::parse($validated['recorded_at']);
            $result = $this->visits->record(
                $actor, $target, $post, $validated['phase'], 'manual',
                (float) $validated['latitude'], (float) $validated['longitude'],
                $isOverride, $validated['client_event_id'], $recordedAt
            );
            $data = $this->visits->checkinResponse($result['log'], $result['target'], $post, $result['idempotent']);
            $data['is_manual_override'] = $isOverride;
            $data['distance_from_post'] = round($distance, 2);

            return ApiResponse::success($data, "Check-in manual berhasil di {$post->nama}", $result['idempotent'] ? 200 : 201);
        } catch (CheckpointException $e) {
            return ApiResponse::error($e->getMessage(), $e->toErrors(), $e->httpStatus);
        }
    }

    /** Catatan terpisah dari log check-in: tidak mengubah kehadiran/progres. */
    public function saveMemberNote(Request $request)
    {
        $data = $request->validate([
            'pendaki_id' => 'required|uuid',
            'post_id' => 'required|integer|exists:gk_posts,id',
            'phase' => 'required|in:naik,turun',
            'note' => 'present|nullable|string|max:500',
        ]);
        [$actor, $target, $error] = $this->resolveTarget($request->user(), $data['pendaki_id']);
        if ($error !== null) {
            return $error;
        }
        $post = GkPost::whereKey($data['post_id'])->where('status', true)->firstOrFail();
        if ((int) $post->id_gate !== (int) $actor->booking->gate_masuk) {
            return ApiResponse::error('Pos tidak berada di jalur booking ini.', null, 404);
        }

        return DB::transaction(function () use ($request, $actor, $target, $post, $data) {
            // Lock yang sama dengan pencatatan check-in agar catatan tidak ditulis setelah hijau.
            $locked = gk_pendaki::whereKey($target->id)->lockForUpdate()->firstOrFail();
            if ((int) $locked->booking->status_booking !== 6) {
                return ApiResponse::error('Pendakian tidak aktif.', null, 409);
            }
            $atPost = fn ($id) => GkCheckpointLog::where('id_booking', $locked->booking_id)
                ->where('id_pendaki', $id)->where('id_post', $post->id)->where('phase', $data['phase'])->exists();
            if ($atPost($locked->id)) {
                return ApiResponse::error('Catatan hanya dapat diubah sebelum anggota check-in.', null, 409);
            }
            if ($actor->id !== $locked->id && ! $atPost($actor->id)) {
                return ApiResponse::error('Ketua harus check-in pada pos dan fase yang sama.', null, 403);
            }
            $note = GkCheckpointMemberNote::updateOrCreate([
                'booking_id' => $locked->booking_id, 'pendaki_id' => $locked->id,
                'post_id' => $post->id, 'phase' => $data['phase'],
            ], ['note' => trim($data['note'] ?? ''), 'updated_by' => $request->user()->id]);

            return ApiResponse::success($note->only(['pendaki_id', 'post_id', 'phase', 'note', 'updated_at']), 'Catatan tersimpan.');
        });
    }

    /**
     * GET /api/tracking/v2/progress/{booking_id}
     */
    public function progress(Request $request, $bookingId)
    {
        $user = $request->user();
        $booking = gk_booking::where('id', $bookingId)
            ->where(function ($query) use ($user) {
                $query->where('id_user', $user->id);
                if ($user->id_bio) {
                    $query->orWhereHas('pendakis', fn ($pendaki) => $pendaki->where('id_bio', $user->id_bio));
                }
            })
            ->firstOrFail();

        try {
            $posts = $this->itinerary->route((int) $booking->gate_masuk);
        } catch (CheckpointException $e) {
            return ApiResponse::error($e->getMessage(), $e->toErrors(), $e->httpStatus);
        }

        $booking->load('pendakis.biodata');
        $pendakiIds = $booking->pendakis->pluck('id');
        $logs = GkCheckpointLog::where('id_booking', $bookingId)
            ->whereIn('id_pendaki', $pendakiIds)
            ->get()
            ->groupBy('id_pendaki');

        $leaderBioId = $booking->user?->id_bio;
        // Urutan deterministik: ketua lebih dulu, sisanya mengikuti urutan created_at.
        $pendakis = $booking->pendakis
            ->sortBy(fn ($p) => $leaderBioId !== null && $p->id_bio === $leaderBioId ? 0 : 1)
            ->values();
        $hikers = [];
        foreach ($pendakis as $pendaki) {
            $hikers[] = $this->itinerary->forPendaki(
                $pendaki->id,
                $pendaki->fullName,
                $leaderBioId !== null && $pendaki->id_bio === $leaderBioId,
                $posts,
                $logs->get($pendaki->id, collect()),
                $pendaki->tracking_phase ?: 'naik'
            );
        }

        $leaderHiker = $user->id_bio ? $booking->pendakis->firstWhere('id_bio', $user->id_bio) : null;

        return ApiResponse::success([
            'booking_id' => $bookingId,
            'gate' => $booking->gateMasuk?->nama,
            'total_posts' => $posts->count(),
            'total_visits' => 2 * $posts->count() - 1,
            'hikers' => $hikers,
            'member_notes' => GkCheckpointMemberNote::where('booking_id', $bookingId)
                ->get(['pendaki_id', 'post_id', 'phase', 'note', 'updated_at']),
            'current_pendaki_id' => $leaderHiker?->id,
        ]);
    }

    /**
     * DELETE /api/tracking/v2/checkpoint/{checkpoint_log_id}
     */
    public function cancelCheckin(Request $request, $checkpointLogId)
    {
        if (! ctype_digit((string) $checkpointLogId) || (int) $checkpointLogId < 1) {
            return ApiResponse::error('Check-in tidak ditemukan', null, 404);
        }

        $actor = $this->getActivePendaki($request->user());
        if (! $actor) {
            return ApiResponse::error('Tidak ada pendakian aktif', null, 403);
        }

        $log = GkCheckpointLog::whereKey($checkpointLogId)
            ->where('id_booking', $actor->booking_id)
            ->whereHas('pendaki', fn ($q) => $q->where('booking_id', $actor->booking_id))
            ->first();
        if (! $log) {
            return ApiResponse::error('Check-in tidak ditemukan', null, 404);
        }

        if ($log->id_pendaki !== $actor->id && $actor->booking->id_user !== $request->user()->id) {
            return ApiResponse::error('Hanya ketua tim yang dapat membatalkan kehadiran anggota', null, 403);
        }

        $target = $actor->booking->pendakis()->whereKey($log->id_pendaki)->firstOrFail();

        try {
            $result = $this->visits->cancel($log, $target);
            $data = $this->visits->cancelResponse($result['logId'], $result['target'], $result['post']);

            return ApiResponse::success($data, "Check-in dibatalkan di {$result['post']->nama}");
        } catch (CheckpointException $e) {
            return ApiResponse::error($e->getMessage(), $e->toErrors(), $e->httpStatus);
        }
    }

    /**
     * Resolve aktor (pendaki login) dan target (anggota yang diwakili, bila ada).
     *
     * @return array{0: gk_pendaki, 1: ?gk_pendaki, 2: ?JsonResponse}
     */
    private function resolveTarget($user, ?string $targetId): array
    {
        $actor = $this->getActivePendaki($user);
        if (! $actor) {
            return [$actor, null, ApiResponse::error('Tidak ada pendakian aktif', null, 403)];
        }

        $target = $actor;
        if ($targetId !== null && $targetId !== $actor->id) {
            if ($actor->booking->id_user !== $user->id) {
                return [$actor, null, ApiResponse::error('Hanya ketua tim yang dapat mewakili anggota', null, 403)];
            }
            $target = $actor->booking->pendakis()->whereKey($targetId)->first();
            if (! $target) {
                return [$actor, null, ApiResponse::error('Pendaki bukan anggota booking aktif', null, 403)];
            }
        }

        return [$actor, $target, null];
    }

    private function getActivePendaki($user): ?gk_pendaki
    {
        if (! $user->id_bio) {
            return null;
        }

        return gk_pendaki::where('id_bio', $user->id_bio)
            ->whereHas('booking', fn ($query) => $query->where('status_booking', 6))
            ->with('booking')
            ->first();
    }
}
