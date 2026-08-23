<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GkSos extends Model
{
    protected $table = 'gk_sos';

    protected $fillable = [
        'id_pendaki', 'id_booking', 'id_destinasi', 'severity',
        'message', 'latitude', 'longitude', 'altitude', 'status',
        'acknowledged_by', 'resolved_by', 'resolved_at', 'resolution_notes',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function pendaki()
    {
        return $this->belongsTo(gk_pendaki::class, 'id_pendaki');
    }

    public function booking()
    {
        return $this->belongsTo(gk_booking::class, 'id_booking');
    }

    public function destinasi()
    {
        return $this->belongsTo(destinasi::class, 'id_destinasi');
    }

    public function chats()
    {
        return $this->hasMany(GkSosChat::class, 'id_sos');
    }

    public function acknowledgedBy()
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
