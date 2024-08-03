<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class gk_gates extends Model
{
    use HasFactory;
    protected $fillable = [
        'nama',
        'status',
        'id_destinasi',
        'max_pendaki_hari',
        'min_pendaki_booking',
        'lokasi',
        'lokasi_maps',
        'detail',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */

    public function destinasi()
    {
        return $this->belongsTo(destinasi::class, 'id_destinasi');
    }
}
