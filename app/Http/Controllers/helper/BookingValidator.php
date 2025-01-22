<?php

namespace App\Http\Controllers\helper;

use App\Http\Controllers\Controller;
use App\Models\gk_booking as ModelsBooking;
use App\Models\gk_pendaki as ModelsPendaki;

use Illuminate\Http\Request;

use function PHPUnit\Framework\returnSelf;

class BookingValidator extends HelperController
{
    public $booking;

    public function __construct($id)
    {
        $this->booking = ModelsBooking::with('gktiket', 'gktiket.tiket_pendaki', 'gateMasuk', 'gateKeluar', 'pendakis')->where('id', $id)->first();
    }

    public function validate()
    {
        // validasi data pendaki
        $pendakis = ModelsPendaki::where('booking_id', $this->booking->id)->get();
        foreach ($pendakis as $pendaki) {
            // 1. Validasi inputan tidak null
            if (empty($pendaki->first_name) || empty($pendaki->last_name) || empty($pendaki->nik)) {
                return [
                    'success' => false,
                    'message' => "Pendaki ID {$pendaki->id}: Data tidak boleh kosong (Nama dan  NIK).",
                ];
            }

            // 2. Validasi file lampiran 
            if (empty($pendaki->lampiran_identitas) || !file_exists($pendaki->lampiran_identitas)) {
                return [
                    'success' => false,
                    'message' => "Pendaki ID {$pendaki->id}: Lampiran identitas wajib diunggah.",
                ];
            }

            if (empty($pendaki->lampiran_surat_kesehatan) || !file_exists($pendaki->lampiran_surat_kesehatan)) {
                return [
                    'success' => false,
                    'message' => "Pendaki ID {$pendaki->id}: Lampiran surat keterangan sehat wajib diunggah.",
                ];
            }

            // 3. Validasi umur pendaki (contoh: usia minimal 17 tahun)
            $usia = (int)abs(now()->diffInYears($pendaki->tanggal_lahir));
            if ($pendaki->usia != $usia) {
                $pendaki->usia = $usia;
                $pendaki->save();
            }
            if ($usia < 17) {
                if (empty($pendaki->lampiran_surat_izin_ortu) || !file_exists($pendaki->lampiran_surat_izin_ortu)) {
                    return [
                        'success' => false,
                        'message' => "Pendaki ID {$pendaki->id}: Pendaki dibawah 17 tahun harus menyertakan surat izin orang tua. {$usia} {$pendaki->tanggal_lahir}",
                    ];
                }
            }

            // 4. Validasi nomor HP
            if (!preg_match('/^[0-9]{10,15}$/', $pendaki->no_hp)) {
                return [
                    'success' => false,
                    'message' => "Pendaki ID {$pendaki->id}: Nomor HP tidak valid.",
                ];
            }

            // 5. Validasi nomor HP darurat
            if (!preg_match('/^[0-9]{10,15}$/', $pendaki->no_hp_darurat)) {
                return [
                    'success' => false,
                    'message' => "Pendaki ID {$pendaki->id}: Nomor HP darurat tidak valid.",
                ];
            }

            // 6. Validasi gambar tidak mengandung konten terlarang (contoh: porno)
            // Gunakan library atau API pihak ketiga jika diperlukan untuk mendeteksi konten terlarang

            // 7. Validasi kecocokan nomor identitas dengan gambar
            // (implementasi ini bergantung pada metode validasi, seperti OCR)

        }

        // validasi data booking
        if (empty($this->booking->id_user) || empty($this->booking->id_tiket) || empty($this->booking->tanggal_masuk) || empty($this->booking->tanggal_keluar)) {
            return [
                'success' => false,
                'message' => "Data booking tidak lengkap. Pastikan ID User, ID Tiket, Tanggal Masuk, dan Tanggal Keluar diisi.",
            ];
        }

        // 1. Cek validasi penugasan (lampiran SIMAKSI atau Surat Tugas wajib ada)
        if (strlen($this->booking->gktiket->penugasan)) {
            if (empty($this->booking->lampiran_stugas) || !file_exists($this->booking->lampiran_stugas)) {
                return [
                    'success' => false,
                    'message' => "Lampiran {$this->booking->gktiket->penugasan} harus diunggah.",
                ];
            }
        }

        // 2. Validasi jumlah pendaki
        $wniCount = $this->booking->pendakis->where('kategori_pendaki', 'wni')->count();
        $wnaCount = $this->booking->pendakis->where('kategori_pendaki', 'wna')->count();
        if ($wniCount + $wnaCount < 1) {
            return [
                'success' => false,
                'message' => "Jumlah pendaki pada booking ID {$this->booking->id} tidak valid. Minimal 1 pendaki WNI",
            ];
        } else {
            $this->booking->total_pendaki_wni = $wniCount;
            $this->booking->total_pendaki_wna = $wnaCount;
            $this->booking->save();
        }

        // 2. Validasi jumlah hari pendakian
        $trakingDays = $this->countTrankingdays($this->booking->tanggal_masuk, $this->booking->tanggal_keluar);
        if ($trakingDays['weekend'] + $trakingDays['weekdays'] < 1) {
            return [
                'success' => false,
                'message' => "Jumlah hari pendakian pada booking ID {$this->booking->id} tidak valid. Minimal 1 hari",
            ];
        } else {
            $this->booking->total_hari = $trakingDays['weekend'] + $trakingDays['weekdays'];
            $this->booking->save();
        }

        // validasi kuota pendakian hari pertama

        // 3. hitung tagihan 
        $tiketwni = $this->booking->gktiket->tiket_pendaki->where('kategori_pendaki', 'wni')->first();
        $tiketwna = $this->booking->gktiket->tiket_pendaki->where('kategori_pendaki', 'wna')->first();
        $totalTagihan = 0;
        foreach ($pendakis as $pendaki) {
            if ($pendaki->kategori_pendaki == 'wni') {
                $tagihan = $tiketwni->harga_masuk_wk * $trakingDays['weekend'] +
                    $tiketwni->harga_masuk_wd * $trakingDays['weekdays'] +
                    $tiketwni->harga_kemah * ($this->booking->total_hari - 1) +
                    $tiketwni->harga_traking +
                    $tiketwni->harga_ansuransi;
            } else if ($pendaki->kategori_pendaki == 'wna') {
                $tagihan = $tiketwna->harga_masuk_wk * $trakingDays['weekend'] +
                    $tiketwna->harga_masuk_wd * $trakingDays['weekdays'] +
                    $tiketwna->harga_kemah * ($this->booking->total_hari - 1) +
                    $tiketwna->harga_traking +
                    $tiketwna->harga_ansuransi;
            }
            $pendaki->tagihan = $tagihan;
            $totalTagihan += $tagihan;
            $pendaki->save();
        }

        // 4. hitung total tagihan
        $this->booking->total_pembayaran = $totalTagihan;
        $this->booking->save();

        // return $this->booking;
        return [
            'success' => true,
            'message' => 'Data Booking Sudah Valid',
            'data' => '',
        ];
    }

    public function validateDataPembayaran()
    {
        // cek pemabayaran mitrans

        // jika belum : validateDataTagihan() 

        // jika sudah : cek total tagihan dengan mitrans

    }

    function generatorUniqueCode($uniq)
    {
        $timestap = time();
        return [
            'success' => true,
            'message' => 'Data Booking Sudah Valid',
            'data' => [
                'uq' => $timestap + '-' + $this->booking->id,
            ],
        ];
    }
    function countTrankingdays($timeStart, $timeEnd)
    {
        $weekdays = 0;
        $weekend = 0;

        $currentDate = strtotime($timeStart);
        $endDate = strtotime($timeEnd);

        while ($currentDate <= $endDate) {
            $dayOfWeek = date('N', $currentDate);

            if ($dayOfWeek >= 6) { // 6 = Saturday, 7 = Sunday
                $weekend++;
            } else {
                $weekdays++;
            }

            $currentDate = strtotime('+1 day', $currentDate);
        }

        return [
            'weekend' => $weekend,
            'weekdays' => $weekdays
        ];
    }
}


// valiadi data penadki
    // cek inputan file
    // cek umur pendaki
    // cek validas no hp *
    // cek validas no hp darurat *
    // cek validas gambar porno *
    // cek validas no identitas dengan gambar *
    // cek data tagihan pendaki

// validasi data booking
    // cek inputan tidak null
    // cek penugasan
    // cek validasi jumlah pendaki di hari pendakian
    // cek jumlah tagihan
    // cek status pembayaran
