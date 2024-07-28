<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class gk_tikets extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_destinasi',
        'id_kategori',
        'id_golongan',
        'id_gate',
        'nama',
        'price_traking',
        'price_kemah',
        'price_ansuransi',
        'min_visitor',
        'penugasan',
        'keterangan',
        'tipe',
        'harga',
    ];

    // Specify the attributes that should be cast to native types
    protected $casts = [
        'penugasan' => 'boolean',
    ];

    public function destinasi()
    {
        return $this->belongsTo(Destinasi::class, 'id_destinasi');
    }

    public function gk_gate()
    {
        return $this->belongsTo(gk_gates::class, 'id_gate');
    }

    public function kategori()
    {
        return $this->belongsTo(kategoris::class, 'id_kategori');
    }

    public function golongan()
    {
        return $this->belongsTo(golongans::class, 'id_golongan');
    }
}
