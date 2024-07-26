<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class gk_pendaki extends Model
{
    use HasFactory;
    protected $table = 'gk_pendakis';

    protected $fillable = [
        'booking_id',
        'wni',
        'nik',
        'nama',
        'lampiran_identitas',
        'no_hp',
        'no_hp_darurat',
        'tanggal_lahir',
        'provinsi',
        'kabupaten',
        'kec',
        'desa',
        'lampiran_surat_kesehatan',
        'lampiran_simaksi',
    ];

    protected $casts = [
        'wni' => 'boolean',
        'tanggal_lahir' => 'date',
    ];

    public function booking()
    {
        return $this->belongsTo(gk_booking::class, 'booking_id');
    }
}
