<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GkOverstayChargeItem extends Model
{
    use \App\Models\Concerns\UsesWibDates;

    protected $table = 'gk_overstay_charge_items';

    protected $fillable = [
        'id_overstay_charge',
        'tanggal',
        'day_type',
        'kategori',
        'harga',
        'source',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function charge(): BelongsTo
    {
        return $this->belongsTo(GkOverstayCharge::class, 'id_overstay_charge');
    }
}
