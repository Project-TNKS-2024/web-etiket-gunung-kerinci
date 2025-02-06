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
    function countWniWna($idbooking)
    {
        $booking = gk_booking::with(['pendakis', 'pendakis.biodata'])->where('id', $idbooking)->first();

        $wni = 0;
        $wna = 0;

        foreach ($booking->pendakis as $pendaki) {
            if ($pendaki->biodata->kenegaraan === 'wni') {
                $wni++;
            } else {
                $wna++;
            }
        }

        return response()->json([
            'wni' => $wni,
            'wna' => $wna
        ]);
    }

    function getDataStruk($idbooking)
    {
        $booking = gk_booking::with([
            'gateMasuk',
            'gateKeluar',
            'pendakis',
            'pendakis.biodata',
            'gateMasuk',
            'gktiket',
            'gktiket.tiket_pendaki',
            'pembayaran',
            'user',
            'user.biodata',
            'destinasi'
        ])->where('id', $idbooking)->first();

        $wkwd = $this->countWeekdaysAndWeekends($booking->tanggal_masuk, $booking->tanggal_keluar);
        $wniwna = $this->countWniWna($booking->id);
        $booking->wkwd = (object) $wkwd->getData(true);
        $booking->wniwna = (object) $wniwna->getData(true);
        return $booking;
    }
}
