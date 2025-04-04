<?php

namespace App\Http\Controllers\helper;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\gk_booking;
use App\Models\gk_pendaki;
use App\Models\gk_tiket_pendaki;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class BookingHelperController extends Controller
{
    /**
     * Menghasilkan kode unik dengan panjang tertentu
     * 
     * @param int $length Panjang kode yang diinginkan
     * @return string Kode unik yang dihasilkan
     */
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

    /**
     * Menghitung total tagihan untuk pendaki dengan menjumlahkan semua komponen getDetailTagihan()
     * 
     * @param object $pendaki Objek data pendaki
     * @return float Hasil penjumlahan dari biaya masuk, biaya berkemah, biaya tracking dan biaya asuransi
     */
    public function getTagihanPendaki($pendaki)
    {
        $tagihan = $this->getDetailTagihan($pendaki);

        return $tagihan['masuk'] + $tagihan['berkemah'] + $tagihan['tracking'] + $tagihan['asuransi'];
    }

    /**
     * Mendapatkan rincian tagihan untuk seorang pendaki
     * 
     * @param object $pendaki Objek data pendaki
     * @return array Rincian tagihan berupa biaya masuk, berkemah, tracking, dan asuransi
     */
    public function getDetailTagihan($pendaki)
    {
        $booking = gk_booking::find($pendaki->booking_id);

        $idtiket = $booking->id_tiket;
        if ($pendaki->biodata->kenegaraan == 'ID') {
            $pendakiNegara = 'wni';
        } else {
            $pendakiNegara = 'wna';
        }
        $tiket = gk_tiket_pendaki::where([
            'id_paket_tiket' => $idtiket,
            'kategori_pendaki' => $pendakiNegara,
        ])->first();


        $hari = $this->countWeekdaysAndWeekends($booking->tanggal_masuk, $booking->tanggal_keluar);

        $tagihanMasuk = $tiket->harga_masuk_wk * $hari->getData()->weekends + $tiket->harga_masuk_wd * $hari->getData()->weekdays;
        $tagihanKemah = ($hari->getData()->weekends + $hari->getData()->weekdays - 1) * $tiket->harga_kemah;
        $tagihanTraking = $tiket->harga_traking;

        $totalDays = $hari->getData()->weekends + $hari->getData()->weekdays;
        $insuranceCostPerPeriod = $tiket->harga_ansuransi;
        $insurancePeriods = ceil($totalDays / $tiket->masa_ansuransi);
        // Calculate the total insurance cost for the entire period

        $tagihanAsuransi = $insurancePeriods * $insuranceCostPerPeriod;

        return [
            'masuk' => $tagihanMasuk,
            'berkemah' => $tagihanKemah,
            'tracking' => $tagihanTraking,
            'asuransi' => $tagihanAsuransi
        ];
    }

    /**
     * Menghitung jumlah hari kerja dan akhir pekan antara dua tanggal
     * 
     * @param string $dateStart Tanggal mulai format Y-m-d
     * @param string $dateEnd Tanggal selesai format Y-m-d
     * @return \Illuminate\Http\JsonResponse Jumlah hari kerja dan akhir pekan
     */
    function countWeekdaysAndWeekends($dateStart, $dateEnd)
    {
        $start = Carbon::createFromFormat(
            'Y-m-d',
            $dateStart,
        );
        $end = Carbon::createFromFormat('Y-m-d', $dateEnd);

        $period = CarbonPeriod::create($start, $end);

        $event = Event::where('libur', 1)
            ->whereBetween('tanggal', [$dateStart, $dateEnd])
            ->get();


        $weekdays = 0;
        $weekends = 0;

        foreach ($period as $date) {
            if ($date->isWeekend()) {
                $weekends++;
            } else if ($event->contains('tanggal', $date->format('Y-m-d'))) {
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

    /**
     * Menghitung jumlah pendaki WNI dan WNA dalam suatu booking
     * 
     * @param gk_booking $booking Objek booking yang akan dihitung
     * @return \Illuminate\Http\JsonResponse Jumlah pendaki WNI dan WNA
     */
    function countWniWna(gk_booking $booking)
    {
        $booking->load('pendakis.biodata');

        $wni = 0;
        $wna = 0;

        foreach ($booking->pendakis as $pendaki) {
            if ($pendaki->biodata->kenegaraan === 'ID') {
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

    /**
     * Mendapatkan data lengkap untuk struk booking
     * 
     * @param int $idbooking ID booking yang akan diambil datanya
     * @return object Data lengkap booking beserta informasi terkait
     */
    function getDataStruk($idbooking)
    {
        $booking = gk_booking::with([
            'gateMasuk',
            'gateKeluar',
            'pendakis',
            'pendakis.biodata',
            'gktiket',
            'gktiket.tiket_pendaki',
            'pembayaran',
            'user',
            'user.biodata',
            'destinasi'
        ])->where('id', $idbooking)->first();

        $wkwd = $this->countWeekdaysAndWeekends($booking->tanggal_masuk, $booking->tanggal_keluar);
        $wniwna = $this->countWniWna($booking);
        $booking->wkwd = (object) $wkwd->getData(true);
        $booking->wniwna = (object) $wniwna->getData(true);
        unset($booking->dataStruk);
        return $booking;
    }
}
