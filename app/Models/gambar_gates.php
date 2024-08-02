<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class gambar_gates extends Model
{
    use HasFactory;

    protected $fillable = [
        'src',
        'id_gate',
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
        return $this->belongsTo(gk_gates::class, 'id_gate');
    }
}
