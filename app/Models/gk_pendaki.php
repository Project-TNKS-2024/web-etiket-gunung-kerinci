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
        return $this->belongsTo(bio_pendaki::class, 'nik');
    }

    public function kategori_pendaki()
    {
        return $this->belongsTo(bio_pendaki::class, 'nik')->select('kategori_pendaki')->value('kategori_pendaki');
    }
    public function first_name()
    {
        return $this->belongsTo(bio_pendaki::class, 'nik')->select('first_name')->value('first_name');
    }
    public function last_name()
    {
        return $this->belongsTo(bio_pendaki::class, 'nik')->select('last_name')->value('last_name');
    }
    public function jenis_kelamin()
    {
        return $this->belongsTo(bio_pendaki::class, 'nik')->select('jenis_kelamin')->value('jenis_kelamin');
    }
    public function tanggal_lahir()
    {
        $tanggal = $this->belongsTo(bio_pendaki::class, 'nik')->select('tanggal_lahir')->value('tanggal_lahir');
        return $tanggal ? Carbon::parse($tanggal) : null;
    }
    public function no_hp()
    {
        return $this->belongsTo(bio_pendaki::class, 'nik')->select('no_hp')->value('no_hp');
    }
    public function no_hp_darurat()
    {
        return $this->belongsTo(bio_pendaki::class, 'nik')->select('no_hp_darurat')->value('no_hp_darurat');
    }
    public function provinsi()
    {
        return $this->belongsTo(bio_pendaki::class, 'nik')->select('provinsi')->value('provinsi');
    }
    public function kabupaten()
    {
        return $this->belongsTo(bio_pendaki::class, 'nik')->select('kabupaten')->value('kabupaten');
    }
    public function kec()
    {
        return $this->belongsTo(bio_pendaki::class, 'nik')->select('kec')->value('kec');
    }
    public function desa()
    {
        return $this->belongsTo(bio_pendaki::class, 'nik')->select('desa')->value('desa');
    }
    public function lampiran_identitas()
    {
        return $this->belongsTo(bio_pendaki::class, 'nik')->select('lampiran_identitas')->value('lampiran_identitas');
    }
}
