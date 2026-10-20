<?php

namespace App\Services;

use Illuminate\Support\Collection;

class CheckpointTimelineService
{
    /**
     * estimated_time_minutes belongs to the OUTGOING segment of each post.
     * An actual check-in anchors subsequent estimates; missing segments break
     * the chain until another actual check-in. No departure time is invented.
     */
    public function checkpoints(Collection $posts, Collection $logs): array
    {
        $arrival = null;
        $previous = null;
        $arrivalIsDummy = false;
        $result = [];

        foreach ($posts->sortBy('urutan') as $post) {
            $minutes = $previous?->estimated_time_minutes;
            $arrival = $arrival !== null && $minutes !== null && $minutes >= 0
                ? $arrival->copy()->addMinutes((int) $minutes)
                : null;
            $arrivalIsDummy = $arrivalIsDummy || ($previous?->info_is_dummy ?? false);
            $log = $logs->firstWhere('id_post', $post->id);

            $result[] = [
                'post_id' => $post->id,
                'nama' => $post->nama,
                'urutan' => $post->urutan,
                'completed' => $log !== null,
                'checked_at' => $log?->checked_at?->copy()->setTimezone('Asia/Jakarta')->toIso8601String(),
                'method' => $log?->method,
                'estimated_arrival_at' => $arrival?->copy()->setTimezone('Asia/Jakarta')->toIso8601String(),
                'travel_minutes_from_previous' => $minutes,
                'estimate_is_dummy' => $arrivalIsDummy,
            ];

            $arrival = $log?->checked_at?->copy() ?? $arrival;
            if ($log?->checked_at !== null) {
                $arrivalIsDummy = false;
            }
            $previous = $post;
        }

        return $result;
    }
}
