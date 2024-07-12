<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class gk_barang_bawaan extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_booking',
        'nama_barang',
        'jumlah',
    ];

    /**
     * Get the booking that owns the barang bawaan.
     */
    public function booking()
    {
        return $this->belongsTo(gk_booking::class, 'id_booking');
    }
}
