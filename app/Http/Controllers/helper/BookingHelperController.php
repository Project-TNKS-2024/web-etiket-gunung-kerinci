<?php

namespace App\Http\Controllers\helper;

use App\Http\Controllers\Controller;
use App\Models\d_Provinsi;
use App\Models\gk_booking;
use App\Models\gk_pendaki;
use App\Models\gk_tiket_pendaki;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

use Illuminate\Http\Request;
use PhpParser\Node\Expr\FuncCall;

class BookingHelperController extends Controller
{
    // function generateUniqueCode(idpendaki)
    public function generateUniqueCode($idpendaki)
    {
        $timestap = time();
        return response()->json([
            'status' => 200,
            'message' => 'berhasil',
            'data' => $timestap
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
                'errors' => $errors
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
        $pendaki = gk_pendaki::where('id', $idpendaki)
            ->with('booking')->first();

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
                'data' => $pendaki
            ]);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'gagal mengambil data pendaki dengan id ' . $idpendaki,
                'data' => $pendaki
            ]);
        }
    }
    public function getTiketPendaki($pendaki)
    {
        $kategori_pendaki = $pendaki->kategori_pendaki;
        $idPaketBooking = $pendaki->booking->id_tiket;
        $tiket = gk_tiket_pendaki::where('id_paket_tiket', $idPaketBooking)
            ->where('kategori_pendaki', $kategori_pendaki)
            ->first();
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
        $idtiket = $pendaki->tiket_id;
        $tiket = gk_tiket_pendaki::find($idtiket);
        $totalDays = $pendaki->booking->total_hari;
        $kategoriDays = $this->getKategoriDay($pendaki->booking->tanggal_masuk);
        if ($kategoriDays == "wk") {
            $tagihanPendaki = $tiket->harga_tiket_wk * $totalDays + $tiket->harga_kemah * ($totalDays - 1) + $tiket->harga_traking + $tiket->harga_ansuransi;
        } else {
            $tagihanPendaki = $tiket->harga_tiket_wd * $totalDays + $tiket->harga_kemah * ($totalDays - 1) + $tiket->harga_traking + $tiket->harga_ansuransi;
        }
        return $tagihanPendaki;
    }
    public function getKategoriDay($tanggal)
    {
        $hari = Carbon::parse($tanggal)->format('l');
        if ($hari == 'Saturday' || $hari == 'Sunday') {
            return "wk";
        } else {
            return "wd";
        }
    }

    public function getDomisili($idprovinsi, $idkabupaten, $idkecamatan, $idkelurahan)
    {
        $provinsi = d_Provinsi::find($idprovinsi);
        $kabupaten = $provinsi->kabupaten()->find($idkabupaten);
        $kecamatan = $kabupaten->kecamatan()->find($idkecamatan);
        $kelurahan = $kecamatan->kelurahan()->find($idkelurahan);
        return [
            'provinsi' => $provinsi->name,
            'kabupaten' => $kabupaten->name,
            'kecamatan' => $kecamatan->name,
            'kelurahan' => $kelurahan->name,
        ];
    }

    public function validasiBooking($idbooking)
    {
        $booking = gk_booking::with(['gateMasuk', 'gateKeluar', 'pendakis'])->where('id', $idbooking)->first();

        // validasi total hari
        $booking->total_hari = $this->getTotalHari($booking->tanggal_masuk, $booking->tanggal_keluar);
        $booking->save();

        // validasi data pendaki
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
            'data' => $booking
        ]);
    }

    function getTotalHari($tanggalMasuk, $tanggalKeluar)
    {
        $tanggalMasuk = Carbon::parse($tanggalMasuk);
        $tanggalKeluar = Carbon::parse($tanggalKeluar);
        $totalHari = $tanggalMasuk->diffInDays($tanggalKeluar);
        return $totalHari;
    }


    function countWeekdaysAndWeekends(
        $dateStart,
        $dateEnd
    ) {
        // Convert string dates to Carbon instances
        $start = Carbon::createFromFormat(
            'd-m-Y',
            $dateStart
        );
        $end = Carbon::createFromFormat('d-m-Y', $dateEnd);

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

        return [
            'weekdays' => $weekdays,
            'weekends' => $weekends
        ];
    }
}
