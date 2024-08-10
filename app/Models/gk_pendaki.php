<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class gk_pendaki extends Model
{
    use HasFactory;
    protected $table = 'gk_pendakis';

    protected $fillable = [
        // data booking
        'booking_id',
        'tiket_id',
        'tagihan',

        // input identias
        'kategori_pendaki',
        'nama',
        'nik',
        'jenis_kelamin',

        // kesehatan
        'tanggal_lahir',
        'usia',
        // berat badan
        // tinggi badan

        // input hp
        'no_hp',
        'no_hp_darurat',

        // input domisili
        'provinsi',
        'kabupaten',
        'kec',
        'desa',

        // input lampiran
        'lampiran_identitas',
        'lampiran_surat_kesehatan',
        'lampiran_surat_izin_ortu',


    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function booking()
    {
        return $this->belongsTo(gk_booking::class, 'booking_id');
    }

    // hubungkan kolom provinsi, kabupaten, kecamatan, kelurahan dengan tabel d_provinsi, d_kabupaten, d_kecamatan, d_kelurahan
    public function provinsi()
    {
        return $this->belongsTo(d_Provinsi::class, 'provinsi');
    }
    public function kabupaten()
    {
        return $this->belongsTo(d_Kabupaten::class, 'kabupaten');
    }
    public function kecamatan()
    {
        return $this->belongsTo(d_Kecamatan::class, 'kec');
    }
    public function kelurahan()
    {
        return $this->belongsTo(d_Kelurahan::class, 'desa');
    }
    public function tiket()
    {
        return $this->belongsTo(gk_tiket_pendaki::class, 'tiket_id');
    }
}
