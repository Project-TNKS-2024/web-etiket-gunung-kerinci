<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GkDisasterReport extends Model
{
    protected $table = 'gk_disaster_reports';

    protected $fillable = [
        'id_user', 'id_destinasi', 'id_booking', 'potensi_bencana',
        'deskripsi', 'lokasi', 'latitude', 'longitude', 'lampiran',
        'status', 'verified_by', 'verified_at', 'notes',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function destinasi()
    {
        return $this->belongsTo(destinasi::class, 'id_destinasi');
    }

    public function booking()
    {
        return $this->belongsTo(gk_booking::class, 'id_booking');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
