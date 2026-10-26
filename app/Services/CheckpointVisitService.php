<?php

namespace App\Services;

use App\Enums\TrackingPhase;
use App\Events\TrackingUpdated;
use App\Exceptions\CheckpointException;
use App\Models\GkCheckpointLog;
use App\Models\GkPost;
use App\Models\GkTracking;
use App\Models\gk_pendaki;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\DB;

/**
 * Mencatat dan membatalkan kunjungan checkpoint dengan aturan fase naik/turun.
 *
 * Controller menangani autentikasi/otorisasi awal; service ini menegakkan
 * invariant domain di dalam transaksi: lock baris pendaki, idempotensi
 * client_event_id, validasi fase terhadap summit, dan peralihan fase.
 */
class CheckpointVisitService
{
    /** Toleransi jam perangkat ke depan (detik). */
    private const FUTURE_TOLERANCE_SECONDS = 300;

    public function __construct(private CheckpointItineraryService $itinerary)
    {
    }

    /** Dipanggil dalam transaksi konfirmasi tiket, setelah baris pendaki dikunci. */
    public function recordTicketBoundary(gk_pendaki $target, TrackingPhase $phase, CarbonInterface $at): GkCheckpointLog
    {
        $booking = $target->booking;
        $gateId = $phase === TrackingPhase::Naik ? $booking->gate_masuk : $booking->gate_keluar;
        $posts = GkPost::where('id_gate', $gateId)->where('status', true)->orderBy('urutan')->get();
        $post = $posts->first();
        if (! $post || $post->is_summit || $posts->where('urutan', $post->urutan)->count() !== 1) {
            throw ValidationException::withMessages(['booking_id' => 'Pos gerbang awal/akhir belum dikonfigurasi dengan benar.']);
        }
        if (! $post->gate || ! $booking->gktiket || (int) $post->gate->id_destinasi !== (int) $booking->gktiket->id_destinasi) {
            throw ValidationException::withMessages(['booking_id' => 'Gerbang tidak sesuai destinasi tiket.']);
        }

        // Kepulangan fisik tidak mensyaratkan rekaman puncak atau mengisi pos yang terlewat.
        return GkCheckpointLog::firstOrCreate([
            'id_pendaki' => $target->id,
            'id_booking' => $booking->id,
            'id_post' => $post->id,
            'phase' => $phase->value,
        ], [
            'client_event_id' => $this->ticketEventId($target, $phase),
            'method' => 'manual',
            'is_manual_override' => false,
            'checked_at' => $at,
        ]);
    }

    public function cancelTicketEntry(gk_pendaki $target): void
    {
        $eventId = $this->ticketEventId($target, TrackingPhase::Naik);
        $logs = $target->checkpointLogs()->where('id_booking', $target->booking_id)->get();
        if ($logs->contains(fn ($log) => $log->client_event_id !== $eventId)) {
            throw ValidationException::withMessages(['pendakis' => 'Check-in tiket tidak dapat dibatalkan karena sudah ada kehadiran pos lain.']);
        }
        $target->checkpointLogs()->where('client_event_id', $eventId)->delete();
        $target->update(['checkin_at' => null, 'tracking_phase' => TrackingPhase::Naik->value]);
    }

    private function ticketEventId(gk_pendaki $target, TrackingPhase $phase): string
    {
        return Uuid::uuid5(Uuid::NAMESPACE_URL, "tnks:ticket:{$target->booking_id}:{$target->id}:{$phase->value}")->toString();
    }

    /** Legacy clients record ascent only and may use routes without v2 configuration. */
    public function recordLegacyAscent(gk_pendaki $target, GkPost $post, array $attributes): GkCheckpointLog
    {
        return DB::transaction(function () use ($target, $post, $attributes) {
            $locked = gk_pendaki::whereKey($target->id)->lockForUpdate()->firstOrFail();
            if (in_array($attributes['method'] ?? null, ['manual', 'gps'], true)) {
                $this->assertWithinPostRadius($post, $attributes['latitude'] ?? null, $attributes['longitude'] ?? null);
                $attributes['is_manual_override'] = false;
            }
            $existing = GkCheckpointLog::where('id_pendaki', $locked->id)
                ->where('id_booking', $locked->booking_id)
                ->where('id_post', $post->id)
                ->where('phase', TrackingPhase::Naik->value)
                ->first();
            if ($existing !== null) {
                throw new CheckpointException('CHECKPOINT_ALREADY_RECORDED', 'Sudah check-in di pos ini', [
                    'checked_at' => $existing->checked_at?->copy()->setTimezone(config('app.timezone'))->toIso8601String(),
                ]);
            }
            $log = GkCheckpointLog::create(array_merge($attributes, [
                'id_pendaki' => $locked->id,
                'id_booking' => $locked->booking_id,
                'id_post' => $post->id,
                'phase' => TrackingPhase::Naik->value,
            ]));
            if ($post->is_summit) {
                $locked->update(['tracking_phase' => TrackingPhase::Turun->value]);
            }
            return $log;
        });
    }

    /**
     * @return array{log: GkCheckpointLog, target: gk_pendaki, idempotent: bool}
     */
    public function record(
        gk_pendaki $actor,
        ?gk_pendaki $target,
        GkPost $post,
        string $phaseString,
        string $method,
        ?float $latitude,
        ?float $longitude,
        bool $isManualOverride,
        ?string $clientEventId,
        ?Carbon $recordedAt,
        array $positionDetails = []
    ): array {
        $phase = TrackingPhase::tryFrom($phaseString);
        if ($phase === null) {
            throw new CheckpointException(
                'CHECKPOINT_PHASE_CONFLICT',
                'Fase perjalanan tidak dikenal.',
                ['phase' => $phaseString],
                422
            );
        }

        $target ??= $actor;
        $recordedAt = ($recordedAt ?? Carbon::now())->copy()->setTimezone(config('app.timezone'));

        return DB::transaction(function () use (
            $actor, $target, $post, $phase, $method, $latitude, $longitude,
            $isManualOverride, $clientEventId, $recordedAt, $positionDetails
        ) {
            // 1. Lock baris pendaki target (urutan lock konsisten: pendaki dulu).
            $locked = gk_pendaki::whereKey($target->id)->lockForUpdate()->firstOrFail();
            $booking = $locked->booking()->firstOrFail();

            // 2. Validasi gate/status pos sebagai pertahanan terakhir.
            if ((int) $post->id_gate !== (int) $booking->gate_masuk) {
                throw new CheckpointException('CHECKPOINT_PHASE_CONFLICT', 'Pos tidak berada di jalur booking ini.', [], 404);
            }
            if (! $post->status) {
                throw new CheckpointException('CHECKPOINT_PHASE_CONFLICT', 'Pos tidak aktif.', [], 404);
            }
            if (in_array($method, ['manual', 'gps'], true)) {
                $this->assertWithinPostRadius($post, $latitude, $longitude);
                $isManualOverride = false;
            }

            // 3. Bukti kehadiran ketua saat mewakili anggota: wajib sesuai fase kunjungan.
            if ($locked->id !== $actor->id) {
                $hasProof = GkCheckpointLog::where('id_pendaki', $actor->id)
                    ->where('id_booking', $booking->id)
                    ->where('id_post', $post->id)
                    ->where('phase', $phase->value)
                    ->exists();
                if (! $hasProof) {
                    throw new CheckpointException(
                        'CHECKPOINT_PHASE_CONFLICT',
                        'Ketua tim harus check-in di pos ini pada fase yang sama terlebih dahulu.',
                        [],
                        403
                    );
                }
            }

            // 4. Validasi waktu kejadian.
            $this->assertRecordedAtValid($recordedAt);

            // 5. Idempotensi + duplikasi.
            $existingByEvent = $clientEventId !== null && $clientEventId !== ''
                ? GkCheckpointLog::where('client_event_id', $clientEventId)->first()
                : null;

            if ($existingByEvent !== null) {
                if ($this->identityMatches($existingByEvent, $locked->id, $booking->id, $post->id, $phase->value)) {
                    return ['log' => $existingByEvent, 'target' => $locked, 'idempotent' => true];
                }
                throw new CheckpointException(
                    'CLIENT_EVENT_CONFLICT',
                    'client_event_id sudah dipakai untuk kejadian berbeda.',
                    ['client_event_id' => $clientEventId]
                );
            }

            $existingByTuple = GkCheckpointLog::where('id_pendaki', $locked->id)
                ->where('id_booking', $booking->id)
                ->where('id_post', $post->id)
                ->where('phase', $phase->value)
                ->first();

            if ($existingByTuple !== null) {
                if ($clientEventId !== null && $existingByTuple->client_event_id === $clientEventId) {
                    return ['log' => $existingByTuple, 'target' => $locked, 'idempotent' => true];
                }
                throw new CheckpointException(
                    'CHECKPOINT_ALREADY_RECORDED',
                    'Sudah check-in di pos ini pada fase ini.',
                    [
                        'checkpoint_log_id' => $existingByTuple->id,
                        'checked_at' => $existingByTuple->checked_at?->copy()->setTimezone(config('app.timezone'))->toIso8601String(),
                        'visit_key' => $phase->value . ':' . $post->id,
                    ]
                );
            }

            // 6. Validasi fase terhadap catatan summit.
            $summit = $this->summitOfGate($booking->gate_masuk);
            $summitLog = GkCheckpointLog::where('id_pendaki', $locked->id)
                ->where('id_booking', $booking->id)
                ->where('id_post', $summit->id)
                ->where('phase', TrackingPhase::Naik->value)
                ->first();

            if ($phase === TrackingPhase::Turun) {
                if ($post->is_summit) {
                    throw new CheckpointException(
                        'CHECKPOINT_PHASE_CONFLICT',
                        'Tidak ada kunjungan summit kedua pada fase turun.',
                        ['visit_key' => TrackingPhase::Turun->value . ':' . $post->id]
                    );
                }
                if ($summitLog === null) {
                    throw new CheckpointException(
                        'DESCENT_REQUIRES_SUMMIT',
                        'Check-in turun membutuhkan check-in puncak terlebih dahulu.',
                        ['summit_post_id' => $summit->id]
                    );
                }
                if ($summitLog->checked_at !== null
                    && $recordedAt->lt($summitLog->checked_at->copy()->subSeconds(self::FUTURE_TOLERANCE_SECONDS))) {
                    throw new CheckpointException(
                        'CHECKPOINT_PHASE_CONFLICT',
                        'Waktu check-in turun mendahului waktu puncak.',
                        ['recorded_at' => $recordedAt->copy()->setTimezone(config('app.timezone'))->toIso8601String(), 'summit_checked_at' => $summitLog->checked_at->copy()->setTimezone(config('app.timezone'))->toIso8601String()]
                    );
                }
            }

            // 7. Kejadian naik yang datang terlambat setelah summit tidak membalik fase.
            if ($phase === TrackingPhase::Naik && ! $post->is_summit && $summitLog !== null) {
                $this->assertLateAscentTime($recordedAt, $summitLog);
            }

            // 8. Buat log dengan fase eksplisit.
            $log = GkCheckpointLog::create([
                'id_pendaki' => $locked->id,
                'id_post' => $post->id,
                'id_booking' => $booking->id,
                'phase' => $phase->value,
                'client_event_id' => $clientEventId !== null && $clientEventId !== '' ? $clientEventId : null,
                'method' => $method,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'is_manual_override' => $isManualOverride,
                'checked_at' => $recordedAt,
            ]);

            // Koordinat diri disimpan bersama checkpoint; replay event tidak membuat titik kedua.
            if ($locked->id === $actor->id && $latitude !== null && $longitude !== null) {
                GkTracking::create([
                    'id_pendaki' => $locked->id,
                    'id_booking' => $booking->id,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'altitude' => $positionDetails['altitude'] ?? null,
                    'accuracy' => $positionDetails['accuracy'] ?? null,
                    'battery_level' => $positionDetails['battery_level'] ?? null,
                    'recorded_at' => isset($positionDetails['position_recorded_at'])
                        ? Carbon::parse($positionDetails['position_recorded_at']) : $recordedAt,
                ]);
                $destinasiId = $booking->destinasi?->id;
                if ($destinasiId) {
                    DB::afterCommit(fn () => broadcast(new TrackingUpdated(
                        destinasiId: $destinasiId,
                        pendakiId: $locked->id,
                        latitude: $latitude,
                        longitude: $longitude,
                        altitude: $positionDetails['altitude'] ?? null,
                        hikerName: $locked->fullName ?? 'Unknown',
                    )));
                }
            }

            // 9. Summit naik -> alihkan fase target ke turun dalam transaksi yang sama.
            if ($phase === TrackingPhase::Naik && $post->is_summit) {
                $locked->update(['tracking_phase' => TrackingPhase::Turun->value]);
            }

            return ['log' => $log, 'target' => $locked, 'idempotent' => false];
        });
    }

    /**
     * Batalkan kunjungan. Summit hanya bisa dibatalkan bila belum ada log turun target.
     *
     * @return array{logId: int, target: gk_pendaki, post: GkPost}
     */
    public function cancel(GkCheckpointLog $log, gk_pendaki $target): array
    {
        $post = $log->post()->firstOrFail();
        $phase = $log->phase ?: TrackingPhase::Naik->value;

        DB::transaction(function () use ($log, $target, $phase, $post) {
            $locked = gk_pendaki::whereKey($target->id)->lockForUpdate()->firstOrFail();
            $log = GkCheckpointLog::whereKey($log->id)
                ->where('id_pendaki', $locked->id)
                ->where('id_booking', $locked->booking_id)
                ->first();
            if ($log === null) {
                throw new CheckpointException('CHECKPOINT_NOT_FOUND', 'Check-in tidak ditemukan.', [], 404);
            }

            if ($log->client_event_id === $this->ticketEventId($locked, TrackingPhase::from($phase))) {
                throw new CheckpointException('TICKET_CONFIRMATION_REQUIRED', 'Kehadiran dari konfirmasi tiket hanya dapat dibatalkan petugas.', [], 403);
            }

            $booking = $locked->booking;
            $gateId = $phase === TrackingPhase::Naik->value ? $booking->gate_masuk : $booking->gate_keluar;
            $confirmedAt = $phase === TrackingPhase::Naik->value ? $locked->checkin_at : $locked->actual_checkout_at;
            if ($confirmedAt && (int) $post->id_gate === (int) $gateId
                && (int) $post->urutan === (int) GkPost::where('id_gate', $gateId)->where('status', true)->min('urutan')) {
                throw new CheckpointException('TICKET_CONFIRMATION_REQUIRED', 'Kehadiran pos gerbang sudah dikonfirmasi petugas.', [], 403);
            }

            if ($phase === TrackingPhase::Naik->value && $post->is_summit) {
                $hasDescent = GkCheckpointLog::where('id_pendaki', $locked->id)
                    ->where('id_booking', $log->id_booking)
                    ->where('phase', TrackingPhase::Turun->value)
                    ->exists();
                if ($hasDescent) {
                    throw new CheckpointException(
                        'SUMMIT_HAS_DESCENT_CHECKINS',
                        'Puncak tidak dapat dibatalkan karena sudah ada check-in turun.',
                        ['checkpoint_log_id' => $log->id]
                    );
                }
                $log->delete();
                $locked->update(['tracking_phase' => TrackingPhase::Naik->value]);
            } else {
                $log->delete();
            }
        });

        return ['logId' => $log->id, 'target' => $target->fresh(), 'post' => $post];
    }

    /**
     * Bentuk respons check-in sukses (record canonical + fase + tujuan + progres).
     */
    public function checkinResponse(GkCheckpointLog $log, gk_pendaki $target, GkPost $post, bool $idempotent): array
    {
        $booking = $target->booking()->firstOrFail();
        $posts = $this->itinerary->route((int) $booking->gate_masuk);
        $logs = GkCheckpointLog::where('id_pendaki', $target->id)
            ->where('id_booking', $booking->id)
            ->get();
        $summary = $this->itinerary->summary($posts, $logs, $target->tracking_phase);
        $phase = $log->phase ?: TrackingPhase::Naik->value;

        return [
            'checkpoint_log_id' => $log->id,
            'client_event_id' => $log->client_event_id,
            'pendaki_id' => $target->id,
            'phase' => $phase,
            'status' => 'completed',
            'checked_at' => $log->checked_at?->copy()->setTimezone(config('app.timezone'))->toIso8601String(),
            'post' => ['id' => $post->id, 'nama' => $post->nama, 'urutan' => $post->urutan],
            'visit_key' => $phase . ':' . $post->id,
            'tracking_phase' => $target->tracking_phase,
            'next_visit_key' => $summary['next_visit_key'],
            'ascent_progress' => $summary['ascent_progress'],
            'descent_progress' => $summary['descent_progress'],
            'overall_progress' => $summary['overall_progress'],
            'idempotent' => $idempotent,
        ];
    }

    /**
     * Bentuk respons pembatalan.
     */
    public function cancelResponse(int $logId, gk_pendaki $target, GkPost $post): array
    {
        $booking = $target->booking()->firstOrFail();
        $posts = $this->itinerary->route((int) $booking->gate_masuk);
        $logs = GkCheckpointLog::where('id_pendaki', $target->id)
            ->where('id_booking', $booking->id)
            ->get();
        $summary = $this->itinerary->summary($posts, $logs, $target->tracking_phase);

        return [
            'checkpoint_log_id' => $logId,
            'pendaki_id' => $target->id,
            'post' => ['id' => $post->id, 'nama' => $post->nama, 'urutan' => $post->urutan],
            'tracking_phase' => $target->tracking_phase,
            'next_visit_key' => $summary['next_visit_key'],
            'ascent_progress' => $summary['ascent_progress'],
            'descent_progress' => $summary['descent_progress'],
            'overall_progress' => $summary['overall_progress'],
        ];
    }

    private function assertWithinPostRadius(GkPost $post, ?float $latitude, ?float $longitude): void
    {
        if (! $post->status) {
            throw new CheckpointException('CHECKPOINT_PHASE_CONFLICT', 'Pos tidak aktif.', [], 404);
        }
        if ($post->latitude === null || $post->longitude === null
            || abs((float) $post->latitude) > 90 || abs((float) $post->longitude) > 180
            || ! is_numeric($post->radius_meter)
            || (float) $post->radius_meter <= 0) {
            throw new CheckpointException('CHECKPOINT_LOCATION_INVALID', 'Lokasi atau radius pos belum dikonfigurasi dengan benar.', [], 422);
        }
        if ($latitude === null || $longitude === null || ! is_finite($latitude) || ! is_finite($longitude)
            || abs($latitude) > 90 || abs($longitude) > 180) {
            throw new CheckpointException('CHECKPOINT_LOCATION_INVALID', 'Koordinat check-in tidak valid.', [], 422);
        }
        $distance = GeoService::distance($latitude, $longitude, (float) $post->latitude, (float) $post->longitude);
        if (! is_finite($distance) || $distance > (float) $post->radius_meter) {
            throw new CheckpointException('CHECKPOINT_OUTSIDE_RADIUS', 'Check-in ditolak: lokasi berada di luar radius pos.', [
                'distance_from_post' => round($distance, 2),
                'radius_meter' => (float) $post->radius_meter,
            ], 422);
        }
    }

    private function identityMatches(GkCheckpointLog $log, string $pendakiId, string $bookingId, int $postId, string $phase): bool
    {
        return $log->id_pendaki === $pendakiId
            && $log->id_booking === $bookingId
            && (int) $log->id_post === $postId
            && ($log->phase ?: TrackingPhase::Naik->value) === $phase;
    }

    private function assertRecordedAtValid(Carbon $recordedAt): void
    {
        if ($recordedAt->gt(Carbon::now()->addSeconds(self::FUTURE_TOLERANCE_SECONDS))) {
            throw new CheckpointException(
                'CHECKPOINT_PHASE_CONFLICT',
                'Waktu check-in berada di masa depan dan butuh penyesuaian jam perangkat.',
                ['recorded_at' => $recordedAt->copy()->setTimezone(config('app.timezone'))->toIso8601String()]
            );
        }
    }

    private function assertLateAscentTime(Carbon $recordedAt, GkCheckpointLog $summitLog): void
    {
        $summitAt = $summitLog->checked_at;
        if ($summitAt !== null && $recordedAt->gt($summitAt->copy()->addSeconds(self::FUTURE_TOLERANCE_SECONDS))) {
            throw new CheckpointException(
                'CHECKPOINT_PHASE_CONFLICT',
                'Waktu check-in naik bertentangan dengan waktu puncak.',
                [
                    'recorded_at' => $recordedAt->copy()->setTimezone(config('app.timezone'))->toIso8601String(),
                    'summit_checked_at' => $summitAt->copy()->setTimezone(config('app.timezone'))->toIso8601String(),
                ]
            );
        }
    }

    private function summitOfGate(int $gateId): GkPost
    {
        return $this->itinerary->route($gateId)->first(fn (GkPost $p) => $p->is_summit);
    }
}
