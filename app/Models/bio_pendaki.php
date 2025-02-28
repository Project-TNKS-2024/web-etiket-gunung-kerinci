<?php

namespace App\Models;

use App\Models\Data\Desa;
use App\Models\Data\Kabupaten;
use App\Models\Data\Kecamatan;
use App\Models\Data\Negara;
use App\Models\Data\Provinsi;
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

    protected $appends = ['fullName', 'dataNegara', 'dataProvinsi', 'dataKabupaten', 'dataKecamatan', 'dataDesa'];


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

    public function Booking()
    {
        return $this->hasManyThrough(
            gk_booking::class,
            gk_pendaki::class,
            'id_bio',
            'id',
            'id',
            'booking_id'
        );
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id_bio', 'id');
    }

    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
    public function getDataNegaraAttribute()
    {
        return Negara::getByCode($this->kenegaraan);
    }
    public function getDataProvinsiAttribute()
    {
        return Provinsi::getByCode($this->provinsi);
    }
    public function getDataKabupatenAttribute()
    {
        return Kabupaten::getByCode($this->kabupaten);
    }
    public function getDataKecamatanAttribute()
    {
        return Kecamatan::getByCode($this->kec);
    }
    public function getDataDesaAttribute()
    {
        return Desa::getByCode($this->desa);
    }
}
