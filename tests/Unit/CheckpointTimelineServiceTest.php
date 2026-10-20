<?php

namespace Tests\Unit;

use Carbon\Carbon;
use App\Models\GkPost;
use App\Services\CheckpointTimelineService;
use Tests\TestCase;

class CheckpointTimelineServiceTest extends TestCase
{
    private function posts(): \Illuminate\Support\Collection
    {
        return collect([
            new GkPost(['id' => 1, 'nama' => 'Gerbang', 'urutan' => 1, 'estimated_time_minutes' => 45]),
            new GkPost(['id' => 2, 'nama' => 'Pos 1', 'urutan' => 2, 'estimated_time_minutes' => 30]),
            new GkPost(['id' => 3, 'nama' => 'Pos 2', 'urutan' => 3, 'estimated_time_minutes' => 15]),
        ])->each(function ($post, $index) { $post->id = $index + 1; });
    }

    public function test_estimates_use_outgoing_segment_and_roll_over_midnight(): void
    {
        $logs = collect([(object) ['id_post' => 1, 'checked_at' => Carbon::parse('2026-10-15T16:45:00Z'), 'method' => 'qr']]);
        $result = (new CheckpointTimelineService)->checkpoints($this->posts(), $logs);

        $this->assertNull($result[0]['estimated_arrival_at']);
        $this->assertSame('2026-10-16T00:30:00+07:00', $result[1]['estimated_arrival_at']);
        $this->assertSame('2026-10-16T01:00:00+07:00', $result[2]['estimated_arrival_at']);
        $this->assertSame(45, $result[1]['travel_minutes_from_previous']);
        $this->assertFalse($result[1]['completed']);
    }

    public function test_missing_duration_breaks_estimates_until_actual_checkin(): void
    {
        $posts = $this->posts();
        $posts[0]->estimated_time_minutes = null;
        $logs = collect([
            (object) ['id_post' => 1, 'checked_at' => Carbon::parse('2026-10-15T01:00:00Z'), 'method' => 'qr'],
            (object) ['id_post' => 2, 'checked_at' => Carbon::parse('2026-10-15T03:00:00Z'), 'method' => 'qr'],
        ]);
        $result = (new CheckpointTimelineService)->checkpoints($posts, $logs);

        $this->assertNull($result[1]['estimated_arrival_at']);
        $this->assertSame('2026-10-15T10:30:00+07:00', $result[2]['estimated_arrival_at']);
        $this->assertTrue($result[1]['completed']);
    }

    public function test_no_checkin_never_invents_a_departure_time(): void
    {
        $result = (new CheckpointTimelineService)->checkpoints($this->posts(), collect());
        $this->assertSame([null, null, null], array_column($result, 'estimated_arrival_at'));
    }
}
