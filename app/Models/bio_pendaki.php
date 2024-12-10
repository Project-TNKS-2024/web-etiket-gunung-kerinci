<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bio_pendaki extends Model
{
    use HasFactory;

    protected $table = 'biodatas';

    protected $fillable = [
        'nik',
        'kategori_pendaki',
        'first_name',
        'last_name',
        'lampiran_identitas',

        'no_hp',
        'no_hp_darurat',

        'jenis_kelamin',
        'tanggal_lahir',

        'provinsi',
        'kabupaten',
        'kec',
        'desa',

    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];
}
