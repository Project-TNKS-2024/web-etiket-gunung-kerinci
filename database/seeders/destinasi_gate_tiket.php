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
            'nama' => 'Gunung Kerinci',
            'status' => true,
            'lokasi' => 'Lokasi Destinasi 1',
            'detail' => 'Deskripsi Destinasi 1',
        ]);

        Destinasi::create([
            'nama' => 'Danau Kaco',
            'status' => true,
            'lokasi' => 'Lokasi Destinasi 2',
            'detail' => 'Deskripsi Destinasi 2',
        ]);

        Destinasi::create([
            'nama' => 'Danau Gunung Tujuh',
            'status' => true,
            'lokasi' => 'Lokasi Destinasi 2',
            'detail' => 'Deskripsi Destinasi 2',
        ]);


        gk_gates::create([
            'nama' => 'Desa Kersik Tuo',
            'lokasi' => 'Lokasi Gate 1',
            'status' => true,
            'detail' => 'Desa Kersik Tuo, Kecamatan Kayu Aro, Kabupaten Kerinci, Jambi.',
        ]);

        gk_gates::create([
            'nama' => 'Solok Selatan',
            'lokasi' => 'Lokasi Gate 2',
            'status' => true,
            'detail' => 'Camping Ground Bangun Rejo di Kabupaten Solok Selatan, Sumatera Barat.',
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
