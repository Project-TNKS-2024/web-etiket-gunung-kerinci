<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class d_Provinsi extends Model
{
    use HasFactory;

    protected $table = 'd_provinsis';

    protected $fillable = [
        "id", "name", "code"
    ];

    public function kabupatens()
    {
        return $this->hasMany(d_Kabupaten::class, 'provinsi_id');
    }
}
