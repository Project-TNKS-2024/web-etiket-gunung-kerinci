<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class GkOverstayPayment extends Model
{
    protected $table = 'gk_overstay_payments';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'id_booking',
        'id_overstay_charge',
        'amount',
        'method',
        'paid_at',
        'recorded_by',
        'reference',
        'note',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(gk_booking::class, 'id_booking');
    }

    public function charge(): BelongsTo
    {
        return $this->belongsTo(GkOverstayCharge::class, 'id_overstay_charge');
    }
}
