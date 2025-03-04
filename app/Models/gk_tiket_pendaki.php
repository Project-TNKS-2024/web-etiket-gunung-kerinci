<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class gk_tiket_pendaki extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_paket_tiket',
        'kategori_pendaki',
        // kategori day diganti dengan harga masuk wk dan wd
        'harga_masuk_wk',
        'harga_masuk_wd',
        'harga_kemah',
        'harga_traking',
        'harga_ansuransi',
        'masa_ansuransi',
    ];

    public function paket_tiket()
    {
        return $this->belongsTo(gk_paket_tiket::class, 'id_paket_tiket');
    }
}
