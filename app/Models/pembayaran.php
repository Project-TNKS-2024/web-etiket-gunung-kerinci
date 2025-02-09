<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class pembayaran extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_booking',
        'amount',
        'status',
        'spesial', //jgn dihapus
        'payment_method', //jgn dihapus
        'bukti_pembayaran', //
        'deadline',
        'keterangan',
    ];

    /**
     * Get the booking that owns the payment.
     */
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
    // Default order by updated_at descending
    protected static function booted()
    {
        static::addGlobalScope('order', function ($query) {
            $query->orderBy('created_at', 'asc');
        });
    }
    public function booking()
    {
        return $this->belongsTo(gk_booking::class, 'id_booking');
    }
}
