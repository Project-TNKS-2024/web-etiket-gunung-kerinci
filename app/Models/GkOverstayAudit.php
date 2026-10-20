<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GkOverstayAudit extends Model
{
    use \App\Models\Concerns\UsesWibDates;

    protected $table = 'gk_overstay_audits';

    protected $fillable = [
        'id_booking',
        'id_pendaki',
        'action',
        'detail',
        'performed_by',
    ];

    protected $casts = [
        'detail' => 'array',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(gk_booking::class, 'id_booking');
    }

    public function pendaki(): BelongsTo
    {
        return $this->belongsTo(gk_pendaki::class, 'id_pendaki');
    }
}
