<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class gk_paket_tiket extends Model
{
    use \App\Models\Concerns\UsesWibDates;

    use HasFactory;
    protected $fillable = [
        'nama',
        'min_pendaki',
        'penugasan',
        'keterangan',
        'id_destinasi',
    ];

    protected $primaryKey = 'id';

    // Specify the attributes that should be cast to native types

    public function tiket_pendaki()
    {
        return $this->hasMany(gk_tiket_pendaki::class, 'id_paket_tiket');
    }

    public function destinasi()
    {
        return $this->belongsTo(destinasi::class, 'id_destinasi');
    }
}
