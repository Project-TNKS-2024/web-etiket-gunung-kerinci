<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class gk_pendaki extends Model
{
    use HasFactory;
    protected $table = 'gk_pendakis';

    protected $fillable = [
        // data booking
        'booking_id',
        'tagihan',

        'id_bio',



        'usia',
        'tinggi',
        'berat',

        // input lampiran
        'lampiran_surat_izin_ortu',

    ];

    public function booking()
    {
        return $this->belongsTo(gk_booking::class, 'booking_id');
    }

    public function biodata()
    {
        return $this->belongsTo(bio_pendaki::class, 'id_bio');
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
}
