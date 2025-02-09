<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class gk_checkpoint extends Model
{
    use HasFactory;
    protected $fillable = [
        'nama',
        'deskripsi_naik',
        'deskripsi_turun',
        'longitude',
        'latitude',
        'urutan',
    ];

    /**
     * Relasi ke tracking (tracking yang dilakukan di checkpoint ini).
     */
    // public function trackings()
    // {
    //     return $this->hasMany(gk_tracking::class, 'gk_checkpoint_id');
    // }
}
