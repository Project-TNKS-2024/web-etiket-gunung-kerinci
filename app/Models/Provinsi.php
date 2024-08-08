<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provinsi extends Model
{
    use HasFactory;

    protected $fillable = [
        "id","name", "code"
    ];

    public function kabupatens() {
        return $this->hasMany(Kabupaten::class, 'provinsi_id');
    }
}
