<?php

namespace Database\Seeders;

use App\Models\destinasi;
use App\Models\gk_gates;
use App\Models\gk_tikets;
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

        gk_tikets::create([
            'id_destinasi' => 1,
            'nama' => 'Tiket Umum',
            'price_traking' => 5000,
            'price_kemah' => 5000,
            'price_ansuransi' => 2500,
            'min_visitor' => 0,
            'penugasan' => false,
            'wni_weekday' => 5000,
            'wni_weekend' => 7500,
            'wna_weekend' => 225000,
            'wna_weekday' => 150000,
            'keterangan' => 'Tiket Umum',
        ]);
        gk_tikets::create([
            'id_destinasi' => 1,
            'nama' => 'Tiket Pelajar',
            'price_traking' => 5000,
            'price_kemah' => 5000,
            'price_ansuransi' => 2500,
            'min_visitor' => 0,
            'penugasan' => false,
            'wni_weekday' => 3000,
            'wni_weekend' => 7500,
            'wna_weekend' => 150000,
            'wna_weekday' => 100000,
            'keterangan' => 'Tiket Pelajar',
        ]);
    }
}
