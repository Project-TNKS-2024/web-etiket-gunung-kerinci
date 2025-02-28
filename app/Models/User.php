<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     * 
     */
    protected $table = 'users';
    protected $fillable = [
        'email',
        'password',
        'role',
        'id_bio',

        'gauth_id', // tambahkan ini
        'gauth_type', // tambahkan ini

        'token',
        'nik_verified_at',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'nik_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function biodata()
    {
        return $this->hasOne(bio_pendaki::class, 'id', 'id_bio',);
    }

    public function booking()
    {
        return $this->hasManyThrough(
            gk_booking::class,  // Model Booking (C)
            gk_pendaki::class,  // Model Pendaki (B)
            'id_bio',           // Foreign key di tabel Pendaki ke Biodata (bio_pendakis.id)
            'id',               // Primary key di tabel Booking (id)
            'id_bio',           // Foreign key di tabel User ke Biodata (bio_pendakis.id)
            'booking_id'        // Foreign key di tabel Pendaki ke Booking (gk_bookings.id)
        );
    }



    // Relasi many-to-many dengan Destinasi
    public function destinasis(): BelongsToMany
    {
        return $this->belongsToMany(destinasi::class, 'destinasi_user')
            ->withPivot('is_penanggungjawab')
            ->withTimestamps();
    }

    // Cek apakah user adalah penanggung jawab suatu destinasi
    public function isPenanggungJawab(destinasi $destinasi): bool
    {
        return $this->destinasis()
            ->where('destinasi_id', $destinasi->id)
            ->wherePivot('is_penanggungjawab', true)
            ->exists();
    }
}
