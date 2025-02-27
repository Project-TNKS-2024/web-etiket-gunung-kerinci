<?php

namespace App\Models;

use App\Models\Data\Negara;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class bio_pendaki extends Model
{
    use HasFactory;

    protected $table = 'biodatas';

    protected $fillable = [
        'nik',
        'kenegaraan',
        'first_name',
        'last_name',
        'lampiran_identitas',

        'no_hp',
        'no_hp_darurat',

        'jenis_kelamin',
        'tanggal_lahir',

        'provinsi',
        'kabupaten',
        'kec',
        'desa',

        'keterangan',
        'verified'

    ];
    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {


            if (empty($model->id)) {
                // Generate a unique 8-character ID
                do {
                    $uuid = substr((string) Str::uuid(), 0, 8);
                } while (static::where('id', $uuid)->exists()); // Ensure uniqueness

                $model->id = $uuid;
            }
        });
    }
    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getNegara()
    {
        return Negara::getByCode($this->kenegaraan);
    }
}
