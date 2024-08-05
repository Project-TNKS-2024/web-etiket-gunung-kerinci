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
        'kategori_hari',
        'harga_masuk',
        'harga_kemah',
        'harga_traking',
        'harga_ansuransi',
    ];

    public function paket_tiket()
    {
        return $this->belongsTo(gk_paket_tiket::class, 'id_paket_tiket');
    }
}
