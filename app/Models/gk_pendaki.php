<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class gk_pendaki extends Model
{
    use HasFactory;
    protected $table = 'gk_pendakis';

    protected $fillable = [
        // data booking
        'booking_id',
        'tagihan',

        'nik',
        //  ====== identitas
        // 'kategori_pendaki',
        // 'first_name',
        // 'last_name',
        // 'jenis_kelamin',
        // 'tanggal_lahir',
        // 'no_hp',
        // 'no_hp_darurat',
        // 'provinsi',
        // 'kabupaten',
        // 'kec',
        // 'desa',
        // 'lampiran_identitas',


        'usia',
        'tinggi',
        'berat',

        // input lampiran
        'lampiran_surat_izin_ortu',

    ];

    public function booking()
    {
        return $this->belongsTo(gk_booking::class, 'booking_id');
    }

    public function biodata()
    {
        return $this->hasOne(bio_pendaki::class, 'nik', 'nik');
    }

    public function getFirstNameAttribute()
    {
        return $this->biodata ? $this->biodata->first_name : null;
    }

    public function getLastNameAttribute()
    {
        return $this->biodata ? $this->biodata->last_name : null;
    }

    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}
