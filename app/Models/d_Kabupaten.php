<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class d_Kabupaten extends Model
{
    use HasFactory;

    protected $table = 'd_kabupatens';

    protected $fillable = [
        "id",
        "type",
        "name",
        "code",
        "full_code",
        "provinsi_id"
    ];

    public function kecamatans()
    {
        return $this->hasMany(d_Kecamatan::class, 'kabupaten_id');
    }

    public function provinsis()
    {
        return $this->belongsTo(d_Provinsi::class, 'provinsi_id');
    }
}
