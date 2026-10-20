<?php

namespace App\Services;

use App\Enums\TrackingPhase;
use App\Exceptions\CheckpointException;
use App\Models\GkCheckpointLog;
use App\Models\GkPost;
use Illuminate\Support\Collection;

/**
 * Membentuk itinerary dua fase (naik lalu turun) untuk satu jalur + satu pendaki,
 * menghitung progres per fase, tujuan berikutnya, dan estimasi tiba.
 *
 * Kontrak segmen:
 *  - Naik   Pi -> P(i+1): jarak Pi.distance_to_next_m, durasi Pi.estimated_time_minutes.
 *  - Turun  Pi -> P(i-1): jarak P(i-1).distance_to_next_m, durasi Pi.estimated_descent_time_minutes.
 *  - Puncak -> pos sebelumnya: jarak P(N-1).distance_to_next_m, durasi PN.estimated_descent_time_minutes.
 */
class CheckpointItineraryService
{
    /**
     * Ambil pos aktif berurutan sebuah jalur dan validasi konfigurasi puncak.
     *
     * @throws CheckpointException bila jalur tidak punya tepat satu puncak aktif,
     *         puncak bukan urutan terakhir, atau urutan pos ambigu.
     */
    public function route(int $gateId): Collection
    {
        $posts = GkPost::where('id_gate', $gateId)
            ->where('status', true)
            ->orderBy('urutan')
            ->get();

        if ($posts->isEmpty()) {
            throw new CheckpointException(
                'ROUTE_CONFIGURATION_INVALID',
                'Jalur belum memiliki pos aktif.',
                [],
                422
            );
        }

        $summits = $posts->filter(fn (GkPost $p) => $p->is_summit);
        if ($summits->count() !== 1) {
            throw new CheckpointException(
                'ROUTE_CONFIGURATION_INVALID',
                'Jalur harus memiliki tepat satu puncak aktif.',
                ['summit_count' => $summits->count()],
                422
            );
        }

        $summit = $summits->first();
        $last = $posts->last();
        if ((int) $summit->urutan !== (int) $last->urutan || $summit->id !== $last->id) {
            throw new CheckpointException(
                'ROUTE_CONFIGURATION_INVALID',
                'Puncak harus berada pada urutan terakhir jalur.',
                [],
                422
            );
        }

        if ($posts->count() !== $posts->unique('urutan')->count()) {
            throw new CheckpointException(
                'ROUTE_CONFIGURATION_INVALID',
                'Urutan pos pada jalur ini tidak unik.',
                [],
                422
            );
        }

        return $posts->values();
    }

    /**
     * Bangun daftar kunjungan (naik lalu turun) untuk satu pendaki.
     * `$logs` adalah koleksi GkCheckpointLog milik pendaki tersebut.
     */
    public function visits(Collection $posts, Collection $logs): array
    {
        $n = $posts->count();
        $logByKey = $logs->keyBy(fn ($l) => $this->phase($l) . ':' . $l->id_post);

        $raw = [];
        $order = 0;

        // Naik: P0 .. P(N-1) (P(N-1) = puncak).
        foreach ($posts as $i => $post) {
            if ($i < $n - 1) {
                $outDistance = $post->distance_to_next_m;
                $outMinutes = $post->estimated_time_minutes;
                $outDummy = (bool) $post->info_is_dummy;
            } else {
                $prev = $posts->get($n - 2);
                $outDistance = $prev?->distance_to_next_m;
                $outMinutes = $post->estimated_descent_time_minutes;
                $outDummy = (bool) $post->info_is_dummy;
            }

            $order++;
            $raw[] = $this->visitFrame(TrackingPhase::Naik, $post, $order, $outDistance, $outMinutes, $outDummy);
        }

        // Turun: P(N-2) .. P0.
        for ($k = $n - 2; $k >= 0; $k--) {
            $post = $posts->get($k);
            $outDistance = $k > 0 ? $posts->get($k - 1)->distance_to_next_m : null;
            $outMinutes = $k > 0 ? $post->estimated_descent_time_minutes : null;
            $outDummy = (bool) $post->info_is_dummy;

            $order++;
            $raw[] = $this->visitFrame(TrackingPhase::Turun, $post, $order, $outDistance, $outMinutes, $outDummy);
        }

        return $this->attachLogsAndEstimates($raw, $logByKey);
    }

    /**
     * Ringkasan progres per fase untuk satu pendaki.
     */
    public function summary(Collection $posts, Collection $logs, string $trackingPhase): array
    {
        $visits = $this->visits($posts, $logs);
        $n = $posts->count();

        $ascentTotal = $n;
        $descentTotal = $n - 1;
        $overallTotal = 2 * $n - 1;

        $ascentCompleted = $this->completedCount($visits, TrackingPhase::Naik);
        $descentCompleted = $this->completedCount($visits, TrackingPhase::Turun);
        $overallCompleted = $ascentCompleted + $descentCompleted;

        // Titik awal turun = pos urutan pertama jalur (P0), kunjungan turun terakhir.
        $firstPostId = $posts->first()->id;
        $routeReturnCompleted = collect($visits)
            ->contains(fn (array $v) => $v['phase'] === TrackingPhase::Turun->value
                && $v['post_id'] === $firstPostId
                && $v['completed']);

        $phase = TrackingPhase::tryFromString($trackingPhase) ?? TrackingPhase::Naik;
        $nextVisitKey = $this->nextVisitKey($visits, $phase);

        return [
            'ascent_progress' => [
                'completed' => $ascentCompleted,
                'total' => $ascentTotal,
                'percentage' => $ascentTotal > 0 ? (int) round($ascentCompleted / $ascentTotal * 100) : 0,
            ],
            'descent_progress' => [
                'completed' => $descentCompleted,
                'total' => $descentTotal,
                'percentage' => $descentTotal > 0 ? (int) round($descentCompleted / $descentTotal * 100) : 0,
            ],
            'overall_progress' => [
                'completed' => $overallCompleted,
                'total' => $overallTotal,
                'percentage' => $overallTotal > 0 ? (int) round($overallCompleted / $overallTotal * 100) : 0,
            ],
            'route_return_completed' => $routeReturnCompleted,
            'next_visit_key' => $nextVisitKey,
        ];
    }

    /**
     * Hasil lengkap per pendaki untuk respons progres v2.
     */
    public function forPendaki(
        string $pendakiId,
        string $nama,
        bool $isLeader,
        Collection $posts,
        Collection $logs,
        string $trackingPhase
    ): array {
        $summary = $this->summary($posts, $logs, $trackingPhase);

        return [
            'pendaki_id' => $pendakiId,
            'nama' => $nama,
            'is_leader' => $isLeader,
            'tracking_phase' => $trackingPhase,
            'next_visit_key' => $summary['next_visit_key'],
            'ascent_progress' => $summary['ascent_progress'],
            'descent_progress' => $summary['descent_progress'],
            'overall_progress' => $summary['overall_progress'],
            'route_return_completed' => $summary['route_return_completed'],
            'visits' => $this->visits($posts, $logs),
        ];
    }

    private function visitFrame(
        TrackingPhase $phase,
        GkPost $post,
        int $order,
        ?int $outDistance,
        ?int $outMinutes,
        bool $outDummy
    ): array {
        return [
            'visit_key' => $phase->value . ':' . $post->id,
            'visit_order' => $order,
            'phase' => $phase->value,
            'post_id' => $post->id,
            'nama' => $post->nama,
            'urutan' => $post->urutan,
            'is_summit' => (bool) $post->is_summit,
            'completed' => false,
            'checkpoint_log_id' => null,
            'checked_at' => null,
            'method' => null,
            'estimated_arrival_at' => null,
            'travel_minutes_from_previous' => null,
            'next_visit_key' => null,
            'distance_to_next_m' => $outDistance,
            'estimated_time_to_next_minutes' => $outMinutes,
            'estimate_is_dummy' => false,
            // internal, dibuang setelah estimasi dihitung:
            '_out_minutes' => $outMinutes,
            '_out_dummy' => $outDummy,
        ];
    }

    private function attachLogsAndEstimates(array $raw, Collection $logByKey): array
    {
        $arrival = null;
        $dummyChain = false;
        $previous = null;

        foreach ($raw as $i => $visit) {
            $inMinutes = $previous['_out_minutes'] ?? null;
            $inDummy = (bool) ($previous['_out_dummy'] ?? false);

            $arrival = ($arrival !== null && $inMinutes !== null && $inMinutes >= 0)
                ? $arrival->copy()->addMinutes((int) $inMinutes)
                : null;
            $dummyChain = $dummyChain || $inDummy;

            $log = $logByKey->get($visit['visit_key']);

            $visit['completed'] = $log !== null;
            $visit['checkpoint_log_id'] = $log?->id;
            $visit['checked_at'] = $log?->checked_at?->copy()->setTimezone('Asia/Jakarta')->toIso8601String();
            $visit['method'] = $log?->method;
            $visit['estimated_arrival_at'] = $arrival?->copy()->setTimezone('Asia/Jakarta')->toIso8601String();
            $visit['travel_minutes_from_previous'] = $inMinutes;
            $visit['estimate_is_dummy'] = $dummyChain;

            $arrival = $log?->checked_at?->copy() ?? $arrival;
            if ($log?->checked_at !== null) {
                $dummyChain = false;
            }

            // Simpan referensi internal untuk iterasi berikutnya, lalu buang dari hasil.
            $previous = $visit;
            unset($visit['_out_minutes'], $visit['_out_dummy']);
            $raw[$i] = $visit;
        }

        return $raw;
    }

    private function completedCount(array $visits, TrackingPhase $phase): int
    {
        return collect($visits)
            ->where('phase', $phase->value)
            ->where('completed', true)
            ->count();
    }

    /**
     * Tujuan berikutnya fase aktif: kunjungan pertama belum tercatat pada fase itu.
     * Null bila seluruh kunjungan fase tersebut sudah tercatat.
     */
    private function nextVisitKey(array $visits, TrackingPhase $phase): ?string
    {
        foreach ($visits as $visit) {
            if ($visit['phase'] === $phase->value && ! $visit['completed']) {
                return $visit['visit_key'];
            }
        }

        return null;
    }

    private function phase($log): string
    {
        return $log->phase ?: TrackingPhase::Naik->value;
    }
}
