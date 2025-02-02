<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class destinasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'status',
        'kategori',
        'lokasi',
        'detail',
        'sop',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    public function gambar_destinasi()
    {
        return $this->hasMany(gambar_destinasi::class, 'id_destinasi');
    }

    public function paket()
    {
        return $this->hasMany(gk_paket_tiket::class, 'id_destinasi');
    }

    public function gates()
    {
        return $this->hasMany(gk_gates::class, 'id_destinasi');
    }
}
