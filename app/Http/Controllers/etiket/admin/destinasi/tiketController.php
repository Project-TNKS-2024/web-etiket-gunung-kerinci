<?php

namespace App\Http\Controllers\etiket\admin\destinasi;

use App\Http\Controllers\Controller;
use App\Models\destinasi as ModelDestinasi;
use App\Models\gk_gates as ModelGates;
use App\Models\gk_paket_tiket as ModelPaketTiket;
use App\Models\gk_tiket_pendaki as ModelTiket;

use Illuminate\Http\Request;


class tiketController extends Controller
{
    public function tiket($id)
    {
        $data = ModelPaketTiket::with(['tiket_pendaki'])->where('id_destinasi', $id)->get();

        $jenisTiket = ['Weekday', 'Weekend'];

        // return $data;

        return view('etiket.admin.destinasi.tiket.tiket', [
            "tiket" => $data,
            "jenisTiket" => $jenisTiket,
            "id_destinasi" => $id
        ]);
    }
    public function add($id)
    {
        // return $id;
        $destinasi = ModelDestinasi::all();
        $gates = ModelGates::all();
        $jenisTiket = ['Weekday', 'Weekend'];

        return view('etiket.admin.destinasi.tiket.tiket.add', [
            "destinasi" => $destinasi,
            "gate" => $gates,
            "jenisTiket" => $jenisTiket,
        ]);
    }
}
