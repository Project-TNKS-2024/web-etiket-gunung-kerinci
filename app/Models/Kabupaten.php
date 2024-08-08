<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kabupaten extends Model
{
    use HasFactory;

    protected $fillable = [
        "id", "type", "name", "code", "full_code", "provinsi_id"
    ];

    public function kecamatans() {
        return $this->hasMany(Kecamatan::class, 'kabupaten_id');
    }

    public function provinsis() {
        return $this->belongsTo(Provinsi::class, 'provinsi_id');
    }
}
