<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    use HasFactory;

    protected $fillable = [
        "id",
        "name",
        "code",
        "full_code",
        "kabupaten_id"
    ];

    public function kelurahans() {
        return $this->hasMany(Kelurahan::class, 'kecamatan_id');
    }

    public function kabupatens() {
        return $this->belongsTo(Kabupaten::class, 'kabupaten_id');
    }
}
