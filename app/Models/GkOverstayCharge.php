<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class GkOverstayCharge extends Model
{
    protected $table = 'gk_overstay_charges';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'id_booking',
        'id_pendaki',
        'status',
        'total',
        'snapshot',
        'finalized_at',
        'finalized_by',
    ];

    protected $casts = [
        'snapshot' => 'array',
        'finalized_at' => 'datetime',
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

    public function pendaki(): BelongsTo
    {
        return $this->belongsTo(gk_pendaki::class, 'id_pendaki');
    }

    public function items(): HasMany
    {
        return $this->hasMany(GkOverstayChargeItem::class, 'id_overstay_charge');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(GkOverstayPayment::class, 'id_overstay_charge');
    }
}
