<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class gk_tikets extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_destinasi',
        'nama',
        'price_traking',
        'price_kemah',
        'price_ansuransi',
        'min_visitor',
        'penugasan',
        'wni_weekday',
        'wni_weekend',
        'wna_weekend',
        'wna_weekday',
        'keterangan',
    ];

    // Specify the attributes that should be cast to native types
    protected $casts = [
        'penugasan' => 'boolean',
    ];

    public function destinasi()
    {
        return $this->belongsTo(Destinasi::class, 'id_destinasi');
    }
}
