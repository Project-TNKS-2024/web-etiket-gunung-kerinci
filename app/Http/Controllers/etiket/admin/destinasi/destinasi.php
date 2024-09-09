<?php

namespace App\Http\Controllers\etiket\admin\destinasi;

use App\Http\Controllers\Controller;
use App\Models\destinasi as ModelsDestinasi;
use App\Models\gambar_destinasi as ModelGambar;
use App\Models\gk_gates as ModelGates;
use App\Models\gk_tiket_pendaki as ModelPendaki;
use Illuminate\Http\Request;

class destinasi extends Controller
{
    public function detail($id)
    {
        $destinasi = ModelsDestinasi::where('id', $id)->first();
        $gambar = ModelGambar::with(['destinasi'])->where('id_destinasi', $id)->get();
        $gates = ModelGates::with(['destinasi'])->where('gk_gates.id_destinasi', $id)->get();
        $tiket = ModelPendaki::with(['paket_tiket'])->get();

        return view('etiket.admin.destinasi.destinasi.detail', [
            'destinasi' => $destinasi,
            'gates' => $gates,
            'gambar' => $gambar,
            'tiket' => $tiket
        ]);
    }
}
