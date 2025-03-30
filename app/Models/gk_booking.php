<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

use function PHPUnit\Framework\isEmpty;

class gk_booking extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_user',
        'id_tiket',
        'tanggal_masuk',
        'tanggal_keluar',
        'total_hari',
        'total_pendaki_wni',
        'total_pendaki_wna',
        'gate_masuk',
        'gate_keluar',
        'status_booking',
        'total_pembayaran',
        'status_pembayaran',
        'dataStruk',
        'unique_code',
        'keterangan',
        'id_booking_master',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    // Generate UUID automatically
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid(); // Generate UUID when creating a new record
            }
        });

        static::deleting(function ($booking) {
            $booking->pendakis()->delete();
            $booking->pembayaran()->delete();
        });
    }
    /**
     * Get the user that owns the booking.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Get the ticket associated with the booking.
     */

    public function gktiket()
    {
        return $this->belongsTo(gk_paket_tiket::class, 'id_tiket');
    }

    /**
     * Get the entry gate for the booking.
     */

    public function gateMasuk()
    {
        return $this->belongsTo(gk_gates::class, 'gate_masuk');
    }

    public function getStatusBooking($id = null)
    {
        if (isEmpty($id)) {
            $id = $this->status_booking;
        }
        // Konversi tanggal untuk perbandingan
        $today = Carbon::today();
        $tanggalMasuk = Carbon::parse($this->tanggal_masuk);
        $tanggalKeluar = Carbon::parse($this->tanggal_keluar);

        // Atur status berdasarkan tanggal
        if ($id >= 4 && $id < 6 && $tanggalMasuk->lessThanOrEqualTo($today)) {
            $id = 41;
        }
        if ($id == 6 && $tanggalKeluar->lessThanOrEqualTo($today)) {
            $id = 61;
        }

        // Daftar status booking
        $status = [
            1  => 'Menyetujui SNK',
            2  => 'Mengisi Formulir',
            3  => 'Menunggu Pembayaran',
            4  => 'Sudah Bayar',
            41 => 'Perlu Konfirmasi Check-in',
            5  => 'Konfirmasi Pendakian',
            6  => 'Check-in',
            61 => 'Perlu Konfirmasi Check-out',
            7  => 'Check-out',
            8  => 'Selesai',
        ];
        return $status[$id];
    }

    /**
     * Get the exit gate for the booking.
     */
    public function gateKeluar()
    {
        return $this->belongsTo(gk_gates::class, 'gate_keluar');
    }

    public function destinasi()
    {
        return $this->hasOneThrough(
            destinasi::class,
            gk_paket_tiket::class,
            'id', // Foreign key di gk_paket_tiket yang berelasi dengan gk_booking
            'id', // Foreign key di destinasi yang berelasi dengan gk_paket_tiket
            'id_tiket', // Foreign key di gk_booking yang merujuk ke gk_paket_tiket
            'id_destinasi' // Foreign key di gk_paket_tiket yang merujuk ke destinasi
        );
    }

    public function pendakis()
    {
        return $this->hasMany(gk_pendaki::class, 'booking_id');
    }



    public function pembayaran()
    {
        return $this->hasMany(pembayaran::class, 'id_booking');
    }
}
