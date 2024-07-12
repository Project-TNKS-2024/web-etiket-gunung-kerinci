<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pembayaran extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_booking',
        'amount',
        'status',
        'spesial',
        'payment_method',
        'deadline',
    ];

    /**
     * Get the booking that owns the payment.
     */
    public function booking()
    {
        return $this->belongsTo(gk_booking::class, 'id_booking');
    }
}
