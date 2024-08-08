<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class d_Kecamatan extends Model
{
    use HasFactory;

    protected $table = 'd_kecamatans';

    protected $fillable = [
        "id",
        "name",
        "code",
        "full_code",
        "kabupaten_id"
    ];

    public function kelurahans()
    {
        return $this->hasMany(d_Kelurahan::class, 'kecamatan_id');
    }

    public function kabupatens()
    {
        return $this->belongsTo(d_Kabupaten::class, 'kabupaten_id');
    }
}
