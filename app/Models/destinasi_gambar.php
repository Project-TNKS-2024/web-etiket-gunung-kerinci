<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class destinasi_gambar extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_destinasi',
        'nama',
        'detail',
        'src',
    ];

    public function destinasi()
    {
        return $this->belongsTo(Destinasi::class, 'id_destinasi');
    }
}
