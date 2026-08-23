<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GkEmergencyMessage extends Model
{
    protected $table = 'gk_emergency_messages';

    protected $fillable = [
        'id_user', 'id_destinasi', 'id_pendaki', 'id_booking',
        'type', 'title', 'description', 'severity',
        'latitude', 'longitude', 'status',
        'acknowledged_by', 'resolved_by', 'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function destinasi()
    {
        return $this->belongsTo(destinasi::class, 'id_destinasi');
    }

    public function pendaki()
    {
        return $this->belongsTo(gk_pendaki::class, 'id_pendaki');
    }

    public function booking()
    {
        return $this->belongsTo(gk_booking::class, 'id_booking');
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
