<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GkCheckpointLog extends Model
{
    protected $table = 'gk_checkpoint_logs';

    protected $fillable = [
        'id_pendaki', 'id_post', 'id_booking', 'method',
        'latitude', 'longitude', 'is_manual_override', 'checked_at',
    ];

    protected $casts = [
        'is_manual_override' => 'boolean',
        'checked_at' => 'datetime',
    ];

    public function pendaki()
    {
        return $this->belongsTo(gk_pendaki::class, 'id_pendaki');
    }

    public function post()
    {
        return $this->belongsTo(GkPost::class, 'id_post');
    }

    public function booking()
    {
        return $this->belongsTo(gk_booking::class, 'id_booking');
    }
}
