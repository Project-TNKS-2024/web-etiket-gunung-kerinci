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
            'harga_masuk_wk' => 7500,
            'harga_masuk_wd' => 5000,
            'harga_kemah' => 5000,
            'harga_traking' => 5000,
            'harga_ansuransi' => 5000,
        ]);

        gk_tiket_pendaki::create([
            'id_paket_tiket' => 2,
            'kategori_pendaki' => 'wni',
            'harga_masuk_wk' => 4500,
            'harga_masuk_wd' => 3000,
            'harga_kemah' => 2500,
            'harga_traking' => 2500,
            'harga_ansuransi' => 2500,
        ]);

        gk_tiket_pendaki::create([
            'id_paket_tiket' => 1,
            'kategori_pendaki' => 'wna',
            'harga_masuk_wk' => 225000,
            'harga_masuk_wd' => 150000,
            'harga_kemah' => 5000,
            'harga_traking' => 5000,
            'harga_ansuransi' => 5000,
        ]);

        gk_tiket_pendaki::create([
            'id_paket_tiket' => 2,
            'kategori_pendaki' => 'wna',
            'harga_masuk_wk' => 150000,
            'harga_masuk_wd' => 100000,
            'harga_kemah' => 2500,
            'harga_traking' => 2500,
            'harga_ansuransi' => 2500,
        ]);
    }
}
