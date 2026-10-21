<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GkPost extends Model
{
    use \App\Models\Concerns\UsesWibDates;

    protected $table = 'gk_posts';

    protected $fillable = [
        'nama', 'urutan', 'latitude', 'longitude', 'altitude',
        'radius_meter', 'id_gate', 'qr_code_value', 'status', 'is_summit', 'detail',
        'mountain_status', 'water_source_status', 'camping_status', 'temperature_c',
        'distance_to_next_m', 'estimated_time_minutes', 'estimated_descent_time_minutes',
        'distance_to_summit_m',
        'info_is_dummy', 'info_note', 'info_updated_at', 'map_image_path',
    ];

    protected $appends = ['map_image_url'];

    public function getMapImageUrlAttribute(): ?string
    {
        $path = $this->map_image_path;
        // Existing databases can keep PNG paths; serve the optimized sibling when available.
        if ($path && str_starts_with($path, 'images/trail-maps/') && str_ends_with($path, '.png')) {
            $webp = substr($path, 0, -4) . '.webp';
            if (is_file(public_path($webp))) {
                $path = $webp;
            }
        }

        return $path ? asset($path) : null;
    }

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'status' => 'boolean',
        'is_summit' => 'boolean',
        'temperature_c' => 'float',
        'distance_to_next_m' => 'integer',
        'estimated_time_minutes' => 'integer',
        'estimated_descent_time_minutes' => 'integer',
        'distance_to_summit_m' => 'integer',
        'info_is_dummy' => 'boolean',
        'info_updated_at' => 'datetime',
    ];

    public function gate()
    {
        return $this->belongsTo(gk_gates::class, 'id_gate');
    }

    public function checkpointLogs()
    {
        return $this->hasMany(GkCheckpointLog::class, 'id_post');
    }
}
