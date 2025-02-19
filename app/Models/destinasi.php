<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class destinasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'status',
        'kategori',
        'lokasi',
        'detail',
        'sop',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    public function gambar_destinasi()
    {
        return $this->hasMany(gambar_destinasi::class, 'id_destinasi');
    }

    public function getStatus($kode = null)
    {
        if (!isset($kode)) { // Gunakan isset agar 0 tetap dihitung
            $kode = $this->status;
        }
        switch ($kode) {
            case 0:
                return 'Close';
            case 1:
                return 'Open';
            case 2:
                return 'Open Booking';
            default:
                return 'Tidak Terdefenisi';
        }
    }

    public function paket()
    {
        return $this->hasMany(gk_paket_tiket::class, 'id_destinasi');
    }

    public function gates()
    {
        return $this->hasMany(gk_gates::class, 'id_destinasi');
    }

    // Relasi many-to-many dengan User
    public function penanggungJawabs(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'destinasi_user')
            ->withPivot('is_penanggungjawab')
            ->withTimestamps();
    }
}
