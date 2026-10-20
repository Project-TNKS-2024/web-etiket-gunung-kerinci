<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

use function PHPUnit\Framework\isEmpty;

class gk_pendaki extends Model
{
    use \App\Models\Concerns\UsesWibDates;

    use HasFactory;
    protected $table = 'gk_pendakis';

    protected $fillable = [
        // data booking
        'booking_id',
        'tagihan',
        'id_bio',
        // 'pemandu',
        'usia',
        // input lampiran
        'lampiran_surat_izin_ortu',
        // waktu kepulangan aktual (overstay)
        'checkin_at',
        'actual_checkout_at',
        'checkout_recorded_at',
        'checkout_recorded_by',
        'checkout_reason',
        // Fase perjalanan pendaki pada booking ini ("naik"/"turun").
        'tracking_phase',

    ];

    protected $casts = [
        'checkin_at' => 'datetime',
        'actual_checkout_at' => 'datetime',
        'checkout_recorded_at' => 'datetime',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

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
        return $this->belongsTo(gk_booking::class, 'booking_id');
    }

    public function tracking()
    {
        return $this->hasMany(GkTracking::class, 'id_pendaki');
    }

    public function checkpointLogs()
    {
        return $this->hasMany(GkCheckpointLog::class, 'id_pendaki');
    }

    public function biodata()
    {
        return $this->belongsTo(bio_pendaki::class, 'id_bio');
    }

    public function getStatus()
    {
        return $this->hasMany(statusPendaki::class, 'id_pendaki');
    }

    public function tarifSnapshot()
    {
        return $this->hasOne(GkPendakiTarifSnapshot::class, 'id_pendaki');
    }

    public function overstayCharge()
    {
        return $this->hasOne(GkOverstayCharge::class, 'id_pendaki');
    }

    public function getFirstNameAttribute()
    {
        return $this->biodata ? $this->biodata->first_name : null;
    }

    public function getLastNameAttribute()
    {
        return $this->biodata ? $this->biodata->last_name : null;
    }

    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}
