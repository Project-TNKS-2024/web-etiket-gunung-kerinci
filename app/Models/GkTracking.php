<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GkTracking extends Model
{
    protected $table = 'gk_tracking';

    protected $fillable = [
        'id_pendaki', 'id_booking', 'latitude', 'longitude',
        'altitude', 'accuracy', 'battery_level', 'recorded_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'recorded_at' => 'datetime',
    ];

    public function pendaki()
    {
        return $this->belongsTo(gk_pendaki::class, 'id_pendaki');
    }

    public function booking()
    {
        return $this->belongsTo(gk_booking::class, 'id_booking');
    }
}
