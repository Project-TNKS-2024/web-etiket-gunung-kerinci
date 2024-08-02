<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class gambar_destinasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'src',
        'nama',
        'detail',
        'id_destinasi',
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

    public function destinasi() {
        return $this->belongsTo(gk_gates::class, 'id_destinasi');
    }
}
