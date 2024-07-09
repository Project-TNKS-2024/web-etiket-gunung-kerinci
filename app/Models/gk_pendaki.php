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
        'usia',
        'tinggi',
        'berat',
        'alamat',
        'provinsi',
        'kabupaten',
        'kec',
        'desa',
        'lampiran_surat_kesehatan',
        'lampiran_simaksi',
        'ketua',
    ];

    protected $casts = [
        'wni' => 'boolean',
        'tanggal_lahir' => 'date',
        'ketua' => 'boolean',
    ];

    public function booking()
    {
        return $this->belongsTo(gk_booking::class, 'booking_id');
    }
}
