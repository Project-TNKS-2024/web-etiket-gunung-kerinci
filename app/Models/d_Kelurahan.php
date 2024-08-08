<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class d_Kelurahan extends Model
{
    use HasFactory;

    protected $table = 'd_kelurahans';

    protected $fillable = [
        "id",
        "name",
        "code",
        "full_code",
        "pos_code",
        "kecamatan_id"
    ];

    public function kecamatan()
    {
        return $this->belongsTo(d_Kecamatan::class, 'kecamatan_id');
    }
}
