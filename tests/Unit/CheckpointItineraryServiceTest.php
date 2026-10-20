<?php

namespace Tests\Unit;

use App\Models\GkPost;
use App\Services\CheckpointItineraryService;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class CheckpointItineraryServiceTest extends TestCase
{
    private function posts(): \Illuminate\Support\Collection
    {
        return collect([
            new GkPost([
                'nama' => 'Base', 'urutan' => 1,
                'distance_to_next_m' => 100, 'estimated_time_minutes' => 10,
                'estimated_descent_time_minutes' => null, 'info_is_dummy' => false,
            ]),
            new GkPost([
                'nama' => 'Pos 1', 'urutan' => 2,
                'distance_to_next_m' => 200, 'estimated_time_minutes' => 20,
                'estimated_descent_time_minutes' => 15, 'info_is_dummy' => false,
            ]),
            new GkPost([
                'nama' => 'Pos 2', 'urutan' => 3,
                'distance_to_next_m' => 300, 'estimated_time_minutes' => 30,
                'estimated_descent_time_minutes' => 25, 'info_is_dummy' => false,
            ]),
            new GkPost([
                'nama' => 'Puncak', 'urutan' => 4, 'is_summit' => true,
                'distance_to_next_m' => 0, 'estimated_time_minutes' => 0,
                'estimated_descent_time_minutes' => 35, 'info_is_dummy' => false,
            ]),
        ])->each(function (GkPost $post, $index) {
            $post->id = $index + 1;
        });
    }

    private function log(int $id, int $postId, string $phase, ?string $checkedAt): object
    {
        return (object) [
            'id' => $id, 'id_post' => $postId, 'phase' => $phase, 'method' => 'qr',
            'checked_at' => $checkedAt !== null ? Carbon::parse($checkedAt) : null,
        ];
    }

    public function test_itinerary_has_2n_minus_1_visits_with_single_summit(): void
    {
        $visits = (new CheckpointItineraryService)->visits($this->posts(), collect());

        $this->assertCount(7, $visits);
        $keys = array_column($visits, 'visit_key');
        $this->assertSame(['naik:1', 'naik:2', 'naik:3', 'naik:4', 'turun:3', 'turun:2', 'turun:1'], $keys);
        $this->assertSame(1, collect($visits)->where('is_summit', true)->count());
        $this->assertSame('naik:4', collect($visits)->firstWhere('is_summit', true)['visit_key']);
        $this->assertNull(collect($visits)->firstWhere('visit_key', 'turun:4'));
    }

    public function test_descent_segments_are_normalized_to_itinerary_direction(): void
    {
        $visits = collect((new CheckpointItineraryService)->visits($this->posts(), collect()))
            ->keyBy('visit_key');

        // Naik: Pi -> P(i+1).
        $this->assertSame(100, $visits['naik:1']['distance_to_next_m']);
        $this->assertSame(10, $visits['naik:1']['estimated_time_to_next_minutes']);
        $this->assertSame(200, $visits['naik:2']['distance_to_next_m']);
        $this->assertSame(20, $visits['naik:2']['estimated_time_to_next_minutes']);

        // Puncak -> pos sebelumnya: jarak milik P3, durasi turun milik puncak.
        $this->assertSame(300, $visits['naik:4']['distance_to_next_m']);
        $this->assertSame(35, $visits['naik:4']['estimated_time_to_next_minutes']);

        // Turun Pi -> P(i-1): jarak P(i-1).distance_to_next_m, durasi Pi.estimated_descent.
        $this->assertSame(200, $visits['turun:3']['distance_to_next_m']);
        $this->assertSame(25, $visits['turun:3']['estimated_time_to_next_minutes']);
        $this->assertSame(100, $visits['turun:2']['distance_to_next_m']);
        $this->assertSame(15, $visits['turun:2']['estimated_time_to_next_minutes']);

        // Titik akhir turun (base) tidak punya segmen keluar.
        $this->assertNull($visits['turun:1']['distance_to_next_m']);
        $this->assertNull($visits['turun:1']['estimated_time_to_next_minutes']);
    }

    public function test_progress_denominators_are_2n_minus_1_and_never_exceed_100(): void
    {
        $posts = $this->posts();
        $logs = collect([
            $this->log(1, 1, 'naik', '2026-10-16T00:00:00Z'),
            $this->log(2, 4, 'naik', '2026-10-16T06:00:00Z'),
            $this->log(3, 3, 'turun', '2026-10-16T07:00:00Z'),
        ]);

        $summary = (new CheckpointItineraryService)->summary($posts, $logs, 'turun');

        $this->assertSame(['completed' => 2, 'total' => 4, 'percentage' => 50], $summary['ascent_progress']);
        $this->assertSame(['completed' => 1, 'total' => 3, 'percentage' => 33], $summary['descent_progress']);
        $this->assertSame(['completed' => 3, 'total' => 7, 'percentage' => 43], $summary['overall_progress']);
        $this->assertFalse($summary['route_return_completed']);
        $this->assertSame('turun:2', $summary['next_visit_key']);
    }

    public function test_next_visit_is_null_when_active_phase_complete(): void
    {
        $posts = $this->posts();
        $logs = collect([
            $this->log(1, 1, 'naik', '2026-10-16T00:00:00Z'),
            $this->log(2, 4, 'naik', '2026-10-16T06:00:00Z'),
            $this->log(3, 3, 'turun', '2026-10-16T07:00:00Z'),
            $this->log(4, 2, 'turun', '2026-10-16T08:00:00Z'),
            $this->log(5, 1, 'turun', '2026-10-16T09:00:00Z'),
        ]);

        $summary = (new CheckpointItineraryService)->summary($posts, $logs, 'turun');
        $this->assertNull($summary['next_visit_key']);
        $this->assertTrue($summary['route_return_completed']);
    }

    public function test_estimate_breaks_on_null_segment_and_resumes_at_next_checkin(): void
    {
        // P3 -> P4 (summit) segmen naik dibuat null untuk memutus rantai.
        $posts = $this->posts();
        $posts->get(2)->estimated_time_minutes = null;

        $logs = collect([
            $this->log(1, 1, 'naik', '2026-10-16T00:00:00Z'),
            $this->log(2, 4, 'naik', '2026-10-16T06:00:00Z'),
        ]);
        $visits = collect((new CheckpointItineraryService)->visits($posts, $logs))->keyBy('visit_key');

        // P1 -> P2 (10m), P2 -> P3 (20m) tersedia; P3 -> P4 terputus (null).
        $this->assertSame('2026-10-16T07:30:00+07:00', $visits['naik:3']['estimated_arrival_at']);
        $this->assertNull($visits['naik:4']['estimated_arrival_at']);
        // Turun memakai anchor summit (06:00) + durasi turun puncak 35m.
        $this->assertSame('2026-10-16T13:35:00+07:00', $visits['turun:3']['estimated_arrival_at']);
    }

    public function test_zero_descent_duration_is_valid_and_not_treated_as_null(): void
    {
        $posts = $this->posts();
        $posts->get(3)->estimated_descent_time_minutes = 0; // puncak -> P3 = 0 menit

        $logs = collect([$this->log(1, 4, 'naik', '2026-10-16T06:00:00Z')]);
        $visits = collect((new CheckpointItineraryService)->visits($posts, $logs))->keyBy('visit_key');

        $this->assertSame(0, $visits['naik:4']['estimated_time_to_next_minutes']);
        $this->assertSame('2026-10-16T13:00:00+07:00', $visits['turun:3']['estimated_arrival_at']);
    }
}
