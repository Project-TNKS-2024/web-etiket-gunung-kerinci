<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GkPost extends Model
{
    protected $table = 'gk_posts';

    protected $fillable = [
        'nama', 'urutan', 'latitude', 'longitude', 'altitude',
        'radius_meter', 'id_gate', 'qr_code_value', 'status', 'detail',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'status' => 'boolean',
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
