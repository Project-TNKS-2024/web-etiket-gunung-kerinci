<?php

namespace App\Services;

class GeoService
{
    private const EARTH_RADIUS_METERS = 6371000;

    /**
     * Calculate distance between two GPS coordinates using Haversine formula.
     * Returns distance in meters.
     */
    public static function distance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLon = deg2rad($lon2 - $lon1);

        $a = sin($deltaLat / 2) ** 2
            + cos($lat1Rad) * cos($lat2Rad) * sin($deltaLon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return self::EARTH_RADIUS_METERS * $c;
    }

    /**
     * Check if a coordinate is within radius of a target point.
     */
    public static function isWithinRadius(
        float $lat, float $lon,
        float $targetLat, float $targetLon,
        float $radiusMeters
    ): bool {
        return self::distance($lat, $lon, $targetLat, $targetLon) <= $radiusMeters;
    }

    /**
     * Find the nearest post from a list of posts given a coordinate.
     * Returns ['post' => Model, 'distance' => float] or null.
     */
    public static function findNearestPost(float $lat, float $lon, $posts): ?array
    {
        $nearest = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($posts as $post) {
            $distance = self::distance($lat, $lon, (float) $post->latitude, (float) $post->longitude);
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearest = $post;
            }
        }

        if (!$nearest) {
            return null;
        }

        return [
            'post' => $nearest,
            'distance' => round($minDistance, 2),
            'within_radius' => $minDistance <= $nearest->radius_meter,
        ];
    }
}
