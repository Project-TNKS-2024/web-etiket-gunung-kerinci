<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class gk_booking extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_user',
        'id_tiket',
        'status',
        'id_booking_master',
        'total_pendaki',
        'wni',
        'wna',
        'keterangan',
        'QR',
        'pembayaran',
        'gate_masuk',
        'gate_keluar',
        'tanggal_masuk',
        'tanggal_keluar',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'integer',
        'total_pendaki' => 'integer',
        'wni' => 'integer',
        'wna' => 'integer',
        'pembayaran' => 'boolean',
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the booking.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Get the ticket associated with the booking.
     */
    public function tiket()
    {
        return $this->belongsTo(Tiket::class, 'id_tiket');
    }

    /**
     * Get the entry gate for the booking.
     */
    public function gateMasuk()
    {
        return $this->belongsTo(gk_gates::class, 'gate_masuk');
    }

    /**
     * Get the exit gate for the booking.
     */
    public function gateKeluar()
    {
        return $this->belongsTo(gk_gates::class, 'gate_keluar');
    }
}
