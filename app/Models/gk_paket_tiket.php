<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class gk_paket_tiket extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_destinasi',
        'nama',
        'min_pendaki',
        'penugasan',
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
