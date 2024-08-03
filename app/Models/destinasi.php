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


    public function gambar_destinasi() {
        return $this->hasMany(gambar_destinasi::class, 'id_destinasi');
    }
}
