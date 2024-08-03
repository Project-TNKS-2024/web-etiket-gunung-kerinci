<?php

namespace Database\Seeders;

use App\Models\gk_paket_tiket;
use App\Models\gk_tiket_pendaki;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class paket_tiket extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // GUNUNG KERINCI KERSIK TUO, NUSANTARA
        gk_paket_tiket::create([
            'id_destinasi' => 1,
            'nama' => 'Umum',
            'min_pendaki' => null,
            'penugasan' => null,
            'keterangan' => 'paket untuk kalangan umum',
        ]);
        gk_paket_tiket::create([
            'id_destinasi' => 1,
            'nama' => 'Rombongan pelajar/mahasiswa',
            'min_pendaki' => 10,
            'penugasan' => 'Surat Pengantar',
            'keterangan' => 'paket untuk kalangan umum',
        ]);


        gk_tiket_pendaki::create([
            'id_paket_tiket' => 1,
            'kategori_pendaki' => 'wni',
            'kategori_hari' => 'wd',
            'harga_masuk' => 5000,
            'harga_kemah' => 5000,
            'harga_traking' => 5000,
            'harga_ansuransi' => 5000,
        ]);

        gk_tiket_pendaki::create([
            'id_paket_tiket' => 2,
            'kategori_pendaki' => 'wni',
            'kategori_hari' => 'wd',
            'harga_masuk' => 3000,
            'harga_kemah' => 2500,
            'harga_traking' => 2500,
            'harga_ansuransi' => 5000,
        ]);

        gk_tiket_pendaki::create([
            'id_paket_tiket' => 1,
            'kategori_pendaki' => 'wni',
            'kategori_hari' => 'wk',
            'harga_masuk' => 7500,
            'harga_kemah' => 5000,
            'harga_traking' => 5000,
            'harga_ansuransi' => 5000,
        ]);

        gk_tiket_pendaki::create([
            'id_paket_tiket' => 2,
            'kategori_pendaki' => 'wni',
            'kategori_hari' => 'wk',
            'harga_masuk' => 4500,
            'harga_kemah' => 2500,
            'harga_traking' => 2500,
            'harga_ansuransi' => 5000,
        ]);


        gk_tiket_pendaki::create([
            'id_paket_tiket' => 1,
            'kategori_pendaki' => 'wna',
            'kategori_hari' => 'wd',
            'harga_masuk' => 150000,
            'harga_kemah' => 5000,
            'harga_traking' => 5000,
            'harga_ansuransi' => 5000,
        ]);

        gk_tiket_pendaki::create([
            'id_paket_tiket' => 2,
            'kategori_pendaki' => 'wni',
            'kategori_hari' => 'wd',
            'harga_masuk' => 100000,
            'harga_kemah' => 2500,
            'harga_traking' => 2500,
            'harga_ansuransi' => 5000,
        ]);

        gk_tiket_pendaki::create([
            'id_paket_tiket' => 1,
            'kategori_pendaki' => 'wni',
            'kategori_hari' => 'wk',
            'harga_masuk' => 225000,
            'harga_kemah' => 5000,
            'harga_traking' => 5000,
            'harga_ansuransi' => 5000,
        ]);

        gk_tiket_pendaki::create([
            'id_paket_tiket' => 2,
            'kategori_pendaki' => 'wni',
            'kategori_hari' => 'wk',
            'harga_masuk' => 150000,
            'harga_kemah' => 2500,
            'harga_traking' => 2500,
            'harga_ansuransi' => 5000,
        ]);
    }
}
