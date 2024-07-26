<?php

namespace App\Http\Controllers\etiket\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class tiket extends Controller
{
    //

    public function daftar() {
        $destinasi = ["Gunung Kerinci", "Gunung Kerinci","Gunung Kerinci", "Gunung Kerinci"];
        $namaTiket = ["Domestik", "Domestik","Mancanegara", "Mancanegara"];
        $gateMasuk = ["Kersik Tuo", "Kersik Tuo","Kersik Tuo", "Kersik Tuo"];
        $jenisTiket = ["Weekday", "Weekend","Weekday", "Weekend"];
        $hargaTiket = [20000, 25000, 210000, 310000];
        $totalTerjual = 122;

        $data = [];
        for ($i = 0; $i < count($destinasi); $i++) {
            $data[$i]["destinasi"] = $destinasi[$i];
            $data[$i]["namaTiket"] = $namaTiket[$i];
            $data[$i]["gateMasuk"] = $gateMasuk[$i];
            $data[$i]["jenisTiket"] = $jenisTiket[$i];
            $data[$i]["hargaTiket"] = $hargaTiket[$i];
        }


        return view('etiket.admin.master-data.tiket', [
            "tiket" => $data,
            "totalTerjual" => $totalTerjual,
        ]);
    }

    public function tambah() {
        return view('etiket.admin.master-data.tiket.tambah');

    }

    public function tambahAction() {
        return null;

    }

    public function edit() {
        return null;

    }

    public function editAction() {
        return null;

    }

    public function delete() {
        return null;
    }
}
