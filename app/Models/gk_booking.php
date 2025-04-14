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
        'validator'
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

        // 40 kadarluarsa
        if ($id == 4 && $tanggalKeluar->lessThan($today)) {
            $id = 40;
        }
        // 50 semua pebdaki batal mendaki
        if ($id == 5) {
            $totalPendaki = $this->total_pendaki_wni + $this->total_pendaki_wna;
            $listRiwayat = $this->riwayatPendakian();
            if ($totalPendaki == $listRiwayat->count()) {
                $allStatusOne = $listRiwayat->every(function ($riwayat) {
                    return $riwayat->status == 1;
                });

                if ($allStatusOne) {
                    $id = 50;
                }
            }
        }
        // 51 perlu konfirmasi check-in, tanggal masuk sudah lewat
        if ($id >= 4 && $id < 6 && $tanggalMasuk->lessThanOrEqualTo($today)) {
            $id = 51;
        }

        // 61 perlu konfirmasi check-out, tanggal keluar sudah lewat
        if ($id == 6 && $tanggalKeluar->lessThanOrEqualTo($today)) {
            $id = 61;
        }
        // Daftar status booking
        $status = [
            0  => 'Menunggu Persetujuan',
            1  => 'Menyetujui SNK',
            2  => 'Mengisi Formulir',
            3  => 'Menunggu Pembayaran',
            4  => 'Sudah Bayar',
            40 => 'Kadarluarsa',
            5  => 'Konfirmasi Pendakian',
            50 => 'Batal Mendaki',
            51 => 'Perlu Konfirmasi Check-in',
            6  => 'Check-in',
            61 => 'Perlu Konfirmasi Check-out',
            7  => 'Check-out',
            8  => 'Selesai',
        ];

        return (object) [
            'code' => $id,
            'status' => $status[$id]
        ];
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

    public function riwayatPendakian()
    {
        return $this->pendakis->flatMap(function ($pendaki) {
            return collect($pendaki->getStatus)->map(function ($status) use ($pendaki) {
                return (object) [
                    'id'          => $status->id,
                    'status'      => $status->status,
                    'statusName'  => $status->statusName(),
                    'validator' => $status->validator,
                    'id_pendaki'  => $status->id_pendaki,
                    'detail'      => $status->detail,
                    'tanggal'     => Carbon::parse($status->created_at)->format('Y-m-d'),
                    'jam'         => Carbon::parse($status->created_at)->format('H:i:s'),
                    'fullName'    => $pendaki->fullName,
                ];
            });
        });
    }


    public function pembayaran()
    {
        return $this->hasMany(pembayaran::class, 'id_booking');
    }
}
