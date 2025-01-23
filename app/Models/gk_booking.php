<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class gk_booking extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_user',
        'id_tiket',
        'tanggal_masuk',
        'tanggal_keluar',
        'kategori_hari', //tidak perlu lagi
        'total_hari',
        'total_pendaki_wni',
        'total_pendaki_wna',
        'gate_masuk',
        'gate_keluar',
        'status_booking',
        'total_pembayaran',
        'status_pembayaran',
        'lampiran_simaksi',
        'lampiran_stugas',
        'unique_code',
        'keterangan',
        'id_booking_master',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    // Generate UUID automatically
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid(); // Generate UUID when creating a new record
            }
        });
    }
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
    public function gktiket()
    {
        return $this->belongsTo(gk_paket_tiket::class, 'id_tiket');
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

    public function pendakis()
    {
        return $this->hasMany(gk_pendaki::class, 'booking_id');
    }
    public function pembayaran()
    {
        return $this->hasMany(pembayaran::class, 'id_booking');
    }
}
