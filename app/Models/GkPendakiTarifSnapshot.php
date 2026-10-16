<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GkPendakiTarifSnapshot extends Model
{
    protected $table = 'gk_pendaki_tarif_snapshots';

    protected $fillable = [
        'id_pendaki',
        'kategori',
        'harga_masuk_wk',
        'harga_masuk_wd',
        'source',
    ];

    public function pendaki(): BelongsTo
    {
        return $this->belongsTo(gk_pendaki::class, 'id_pendaki');
    }
}
