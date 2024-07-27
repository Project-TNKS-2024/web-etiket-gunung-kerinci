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
            'foto' => 'https://upload.wikimedia.org/wikipedia/commons/f/f4/Uprising-mount_kerinci.jpg',
        ]);

        Destinasi::create([
            'nama' => 'Danau Kaco',
            'status' => false,
            'lokasi' => 'Lokasi Destinasi 2',
            'detail' => 'Deskripsi Destinasi 2',
            'foto' => '-',
        ]);

        Destinasi::create([
            'nama' => 'Danau Gunung Tujuh',
            'status' => true,
            'lokasi' => 'Lokasi Destinasi 2',
            'detail' => 'Deskripsi Destinasi 2',
            'foto' => '-',
        ]);


        gk_gates::create([
            'nama' => 'Desa Kersik Tuo',
            'lokasi' => 'Lokasi Gate 1',
            'status' => true,
            'foto' => 'https://upload.wikimedia.org/wikipedia/commons/f/f4/Uprising-mount_kerinci.jpg',
            'detail' => 'Desa Kersik Tuo, Kecamatan Kayu Aro, Kabupaten Kerinci, Jambi.',
        ]);

        gk_gates::create([
            'nama' => 'Solok Selatan',
            'lokasi' => 'Lokasi Gate 2',
            'status' => true,
            'foto' => '-',
            'detail' => 'Camping Ground Bangun Rejo di Kabupaten Solok Selatan, Sumatera Barat.',
        ]);

        tiket::create([
            'id_destinasi' => 1,
            'nama' => 'Domestik',
            'spesial' => 'gunungKerinci',
            'keterangan' => '-',
            'harga wna' => 40000,
            'harga wni' => 50000,
            'gate' => 1,
            'jenisTiket' => 1,
            'harga' => 20000,
        ]);
    }
}
