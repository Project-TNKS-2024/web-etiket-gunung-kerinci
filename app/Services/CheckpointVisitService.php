<?php

namespace App\Services;

use App\Enums\TrackingPhase;
use App\Exceptions\CheckpointException;
use App\Models\GkCheckpointLog;
use App\Models\GkPost;
use App\Models\gk_pendaki;
use Carbon\Carbon;
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

    /** Legacy clients record ascent only and may use routes without v2 configuration. */
    public function recordLegacyAscent(gk_pendaki $target, GkPost $post, array $attributes): GkCheckpointLog
    {
        return DB::transaction(function () use ($target, $post, $attributes) {
            $locked = gk_pendaki::whereKey($target->id)->lockForUpdate()->firstOrFail();
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
        ?Carbon $recordedAt
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
            $isManualOverride, $clientEventId, $recordedAt
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
