<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class gk_paket_tiket extends Model
{
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
    protected $casts = [
        'penugasan' => 'boolean',
    ];

    public function destinasi()
    {
        return $this->belongsTo(Destinasi::class, 'id_destinasi');
    }

    public function tiket_pendaki() {
        return $this->belongsTo(gk_tiket_pendaki::class, "id", "id_paket_tiket");
    }
}
