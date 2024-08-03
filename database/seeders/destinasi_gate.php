<?php

namespace Database\Seeders;

use App\Models\destinasi;
use App\Models\gk_gates;
use Illuminate\Database\Seeder;

class destinasi_gate extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // destinasi::create([
        //     'nama' => 'Gunung Kerinci',
        //     'status' => true,
        //     'lokasi' => 'Lokasi Destinasi 1',
        //     'detail' => 'Deskripsi Destinasi 1',
        //     'foto' => 'https://upload.wikimedia.org/wikipedia/commons/f/f4/Uprising-mount_kerinci.jpg',
        // ]);

        destinasi::create([
            'nama' => 'Gunung Kerinci',
            'status' => true,
            'lokasi' => 'Lokasi Destinasi 1',
            'detail' => 'Deskripsi Destinasi 1',
        ]);

        Destinasi::create([
            'nama' => 'Danau Kaco',
            'status' => false,
            'detail' => 'Deskripsi Destinasi 2',
            'lokasi' => 'Lokasi Destinasi 2',
        ]);

        Destinasi::create([
            'nama' => 'Danau Gunung Tujuh',
            'status' => true,
            'lokasi' => 'Lokasi Destinasi 3',
            'detail' => 'Deskripsi Destinasi 3',
        ]);



        gk_gates::create([
            'nama' => 'Desa Kersik Tuo',
            'status' => true,
            'id_destinasi' => '1',
            'max_pendaki_hari' => 30,
            'min_pendaki_booking' => null,
            'lokasi_maps' => '-',
            'lokasi' => 'Lokasi Gate 1',
            'detail' => 'Desa Kersik Tuo, Kecamatan Kayu Aro, Kabupaten Kerinci, Jambi.',
        ]);

        gk_gates::create([
            'nama' => 'Solok Selatan',
            'status' => true,
            'id_destinasi' => '1',
            'max_pendaki_hari' => 46,
            'min_pendaki_booking' => null,
            'lokasi_maps' => '-',
            'lokasi' => 'Lokasi Gate 2',
            'detail' => 'Camping Ground Bangun Rejo di Kabupaten Solok Selatan, Sumatera Barat.',
        ]);
    }
}
