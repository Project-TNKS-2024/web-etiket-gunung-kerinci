<?php

namespace App\Http\Controllers\helper;

use App\Http\Controllers\Controller;
use App\Models\gk_booking as ModelsBooking;
use App\Models\gk_pendaki as ModelsPendaki;

use Illuminate\Http\Request;

class BookingValidator extends HelperController
{
    public $booking;

    public function __construct($id)
    {
        $this->booking = ModelsBooking::where('id', $id)->first();
    }

    public function validate()
    {
        // validasi data pendakian
        $this->validateDataBooking();

        // validasi data pendaki
        $this->validateDataPendaki();

        // vaalidasi data tagihan
        $this->validateDataTagihan();

        // validasi data pembayaran
        $this->validateDataPembayaran();

        return $this->response($this->booking, "Validasi berhasil", 200);
    }
    public function validateDataBooking()
    {
        // cek tanggal masuk lebih kecil dari tanggal keluar

        // cek jumlah pendaki, pendaki yang lebih di hapus

        // cek jumlah pendaki wna wni

        // cek kelengkapan input penugasan

        // cek ketersediaan tiket pendakian

    }
    public function validateDataPendaki()
    {
        // cek inputan tidak null

        // cek inputan file

        // cek umur pendaki

        // cek validas no hp *

        // cek validas no hp darurat *

        // cek validas gambar porno *

        // cek validas no identitas dengan gambar *

        // cek data tagihan pendaki

    }
    public function validateDataTagihan()
    {
        // jumlahkan tagihan dari semua pendaki

        // cocokkan dengan total pembayaran
    }

    public function validateDataPembayaran()
    {
        // cek pemabayaran mitrans

        // jika belum : validateDataTagihan() 

        // jika sudah : cek total tagihan dengan mitrans

    }

    // function generatorUniqueCode($uniq){}
    // function ContTraking($timeStart, $timeEnd, $type = ["days", "hours"]){}
}
