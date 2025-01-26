<?php

namespace App\Http\Controllers\helper;

use App\Http\Controllers\Controller;
use App\Models\gk_booking;
use App\Models\gk_pendaki;
use App\Models\gk_tiket_pendaki;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

use Illuminate\Http\Request;
use PhpParser\Node\Expr\FuncCall;

class BookingHelperController extends Controller
{

    function generateCode($length)
    {
        // Define the character pool
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $charactersLength = strlen($characters);
        $uniqueCode = '';

        // Randomly pick characters from the pool
        for ($i = 0; $i < $length; $i++) {
            $uniqueCode .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $uniqueCode;
    }

    public function generateUniqueCode($idbooking)
    {
        $timestap = time();
        return response()->json([
            'status' => 200,
            'message' => 'berhasil',
            'data' => $timestap + $idbooking,
        ]);
    }

    public function validasiFormulirPendaki($pendaki)
    {
        // Validasi kolom wajib
        $requiredFields = [
            'kategori_pendaki' => 'Kategori Pendaki',
            'nama' => 'Nama',
            'nik' => 'NIK',
            'lampiran_identitas' => 'Lampiran Identitas',
            'no_hp' => 'Nomor HP',
            'no_hp_darurat' => 'Nomor HP Darurat',
            'jenis_kelamin' => 'Jenis Kelamin',
            'tanggal_lahir' => 'Tanggal Lahir',
            'usia' => 'Usia',
            'provinsi' => 'Provinsi',
            'kabupaten' => 'Kabupaten',
            'kec' => 'Kecamatan',
            'desa' => 'Desa',
            'lampiran_surat_kesehatan' => 'Lampiran Surat Kesehatan',
        ];

        $errors = [];

        foreach ($requiredFields as $field => $fieldName) {
            if (empty($pendaki[$field])) {
                $errors[] = "$fieldName tidak boleh kosong.";
            }
        }

        // Jika ada error, return response dengan status gagal
        if (!empty($errors)) {
            return response()->json([
                'status' => 400,
                'message' => 'Validasi formulir pendaki gagal.',
                'errors' => $errors,
            ]);
        }

        // Jika validasi sukses, return status sukses
        return response()->json([
            'status' => 200,
            'message' => 'Formulir pendaki valid.',
        ]);
    }
    // function validatePendaki(idpendaki)
    public function validatDataPendaki($idpendaki)
    {
        $pendaki = gk_pendaki::where('id', $idpendaki)->with('booking')->first();

        if ($pendaki) {
            // validasi tiket pendaki
            $tiketPendaki = $this->getTiketPendaki($pendaki);
            $pendaki->tiket_id = $tiketPendaki->id;

            // get umur pendaki
            $pendaki->usia = $this->getUmurPendaki($pendaki->tanggal_lahir);

            // get tagihan
            $pendaki->tagihan = $this->getTagihanPendaki($pendaki);

            // update pendaki
            $pendaki->save();

            return response()->json([
                'status' => 200,
                'message' => 'berhasil mengambil data pendaki dengan id ' . $idpendaki,
                'data' => $pendaki,
            ]);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'gagal mengambil data pendaki dengan id ' . $idpendaki,
                'data' => $pendaki,
            ]);
        }
    }
    public function getTiketPendaki($pendaki)
    {
        $kategori_pendaki = $pendaki->kategori_pendaki;
        $idPaketBooking = $pendaki->booking->id_tiket;
        $tiket = gk_tiket_pendaki::where('id_paket_tiket', $idPaketBooking)->where('kategori_pendaki', $kategori_pendaki)->first();
        return $tiket;
    }
    public function getUmurPendaki($tanggalLahir)
    {
        $tanggalLahir = Carbon::parse($tanggalLahir);
        $umur = $tanggalLahir->diffInYears(Carbon::now());
        return $umur;
    }

    public function getTagihanPendaki($pendaki)
    {
        $tagihan = $this->getDetailTagihan($pendaki);

        return $tagihan['masuk'] + $tagihan['berkemah'] + $tagihan['tracking'] + $tagihan['asuransi'];
    }

    public function getDetailTagihan($pendaki)
    {
        $booking = gk_booking::find($pendaki->booking_id);
        $idtiket = $booking->id_tiket;
        $tiket = gk_tiket_pendaki::where([
            'id_paket_tiket' => $idtiket,
            'kategori_pendaki' => $pendaki->biodata->kenegaraan,
        ])->first();

        // dd($tiket);

        $hari = $this->countWeekdaysAndWeekends($booking->tanggal_masuk, $booking->tanggal_keluar);

        $tagihanMasuk = $tiket->harga_masuk_wk * $hari->getData()->weekends + $tiket->harga_masuk_wd * $hari->getData()->weekdays;
        $tagihanKemah = ($hari->getData()->weekends + $hari->getData()->weekdays - 1) * $tiket->harga_kemah;
        $tagihanTraking = $tiket->harga_traking;

        $totalDays = $hari->getData()->weekends + $hari->getData()->weekdays;
        $insuranceCostPerPeriod = $tiket->harga_ansuransi; // Misalnya, Rp 50,000 per tiga hari
        $insurancePeriods = ceil($totalDays / 3); // Pembulatan ke atas
        $tagihanAsuransi = $insurancePeriods * $insuranceCostPerPeriod;

        return [
            'masuk' => $tagihanMasuk,
            'berkemah' => $tagihanKemah,
            'tracking' => $tagihanTraking,
            'asuransi' => $tagihanAsuransi
        ];
    }


    public function validasiBooking($idbooking)
    {
        $booking = gk_booking::with(['gateMasuk', 'gateKeluar', 'pendakis'])
            ->where('id', $idbooking)
            ->first();

        // validasi total hari
        $booking->total_hari = $this->getTotalHari($booking->tanggal_masuk, $booking->tanggal_keluar);
        $booking->save();

        // validasi jumlah pendaki
        $totalPendaki = $booking->total_pendaki_wna + $booking->total_pendaki_wni;
        $pendakis = $booking->pendakis;

        // hapus pendaki yang kelebihan
        if ($pendakis->count() > $totalPendaki) {
            $pendakisToDelete = $pendakis->slice($totalPendaki); // Ambil pendaki yang kelebihan
            foreach ($pendakisToDelete as $pendaki) {
                $pendaki->delete(); // Hapus pendaki yang kelebihan
            }
        }
        $pendakis = $booking->pendakis()->get();

        // validasi data pendaki
        foreach ($pendakis as $pendaki) {
            $this->validatDataPendaki($pendaki->id);
        }
        $pendakis = $booking->pendakis()->get();

        // validasi total pendaki wna wni
        $booking->total_pendaki_wna = $pendakis->where('kategori_pendaki', 'wna')->count();
        $booking->total_pendaki_wni = $pendakis->where('kategori_pendaki', 'wni')->count();
        $booking->save();

        // validasi total tagihan
        $booking->total_pembayaran = $pendakis->sum('tagihan');
        $booking->save();

        return response()->json([
            'status' => 200,
            'message' => 'berhasil mengambil data booking dengan id ' . $idbooking,
            'data' => $booking,
        ]);
    }

    function getTotalHari($tanggalMasuk, $tanggalKeluar)
    {
        $tanggalMasuk = Carbon::parse($tanggalMasuk);
        $tanggalKeluar = Carbon::parse($tanggalKeluar);
        $totalHari = $tanggalMasuk->diffInDays($tanggalKeluar);
        return $totalHari + 1;
    }

    function countWeekdaysAndWeekends($dateStart, $dateEnd)
    {
        // Convert string dates to Carbon instances
        $start = Carbon::createFromFormat(
            // 'd-m-Y',
            'Y-m-d',
            $dateStart,
        );
        // return $dateStart;
        $end = Carbon::createFromFormat('Y-m-d', $dateEnd);

        // Create a CarbonPeriod instance
        $period = CarbonPeriod::create($start, $end);

        // Initialize counters
        $weekdays = 0;
        $weekends = 0;

        // Iterate through each date in the period
        foreach ($period as $date) {
            if ($date->isWeekend()) {
                $weekends++;
            } else {
                $weekdays++;
            }
        }

        return response()->json([
            'weekdays' => $weekdays,
            'weekends' => $weekends,
        ]);
    }
}
