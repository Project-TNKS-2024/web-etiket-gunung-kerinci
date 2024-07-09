<?php

namespace Database\Seeders;

use App\Models\destinasi;
use App\Models\gk_gates;
use App\Models\tiket;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class destinasi_gate_tiket extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        destinasi::create([
            'nama' => 'Destinasi 1',
            'status' => true,
            'lokasi' => 'Lokasi Destinasi 1',
            'detail' => 'Deskripsi Destinasi 1',
        ]);

        Destinasi::create([
            'nama' => 'Destinasi 2',
            'status' => true,
            'lokasi' => 'Lokasi Destinasi 2',
            'detail' => 'Deskripsi Destinasi 2',
        ]);


        gk_gates::create([
            'nama' => 'Gate 1',
            'lokasi' => 'Lokasi Gate 1',
            'status' => true,
            'detail' => 'Deskripsi Gate 1',
        ]);

        gk_gates::create([
            'nama' => 'Gate 2',
            'lokasi' => 'Lokasi Gate 2',
            'status' => true,
            'detail' => 'Deskripsi Gate 2',
        ]);

        tiket::create([
            'id_destinasi' => 1,
            'wni' => true,
            'nama' => 'tiket 1',
            'spesial' => 'gunung_kerinci',
            'keterangan' => '-',
            'harga' => 40000,
        ]);
        tiket::create([
            'id_destinasi' => 1,
            'wni' => false,
            'nama' => 'tiket 1',
            'spesial' => 'gunung_kerinci',
            'keterangan' => '-',
            'harga' => 40000,
        ]);
    }
}
