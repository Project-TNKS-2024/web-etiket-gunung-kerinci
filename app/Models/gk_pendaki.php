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
        'tiket_id',
        'kategori_pendaki',
        'nama',
        'nik',
        'lampiran_identitas',
        'no_hp',
        'no_hp_darurat',
        'tanggal_lahir',
        'usia',
        'provinsi',
        'kabupaten',
        'kec',
        'desa',
        'lampiran_surat_kesehatan',
        'lampiran_surat_izin_ortu',
        'tagihan',
        'jenis_kelamin',
        'jenis_ identitas',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function booking()
    {
        return $this->belongsTo(gk_booking::class, 'booking_id');
    }
}
