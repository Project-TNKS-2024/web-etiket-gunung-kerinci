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
            'min_pendaki' => 2,
            'penugasan' => null,
            'keterangan' => 'Paket ini dirancang untuk individu atau kelompok kecil yang ingin menikmati pengalaman mendaki Gunung Kerinci. Minimal 2 pendaki.',
        ]);
        gk_paket_tiket::create([
            'id_destinasi' => 1,
            'nama' => 'Rombongan pelajar/mahasiswa',
            'min_pendaki' => 10,
            'penugasan' => 'Surat Pengantar',
            'keterangan' => 'Paket ini dirancang khusus untuk kelompok besar pelajar atau mahasiswa yang ingin melakukan kegiatan pendakian sebagai bagian dari kegiatan sekolah, kampus, atau organisasi. Minimal 10 pendaki.',
        ]);

        // @bbtn_kerinciseblatofficial 24-okt-2024
        // @bbtn_kerinciseblatofficial 16-jann-2025
        gk_tiket_pendaki::create([
            'id_paket_tiket' => 1,
            'kategori_pendaki' => 'wni',
            'harga_masuk_wk' => 10000,
            'harga_masuk_wd' => 15000,
            'harga_kemah' => 5000,
            'harga_traking' => 20000,
            'harga_ansuransi' => 2500,
            'masa_ansuransi' => 3,
        ]);
        gk_tiket_pendaki::create([
            'id_paket_tiket' => 1,
            'kategori_pendaki' => 'wna',
            'harga_masuk_wk' => 150000,
            'harga_masuk_wd' => 150000,
            'harga_kemah' => 5000,
            'harga_traking' => 20000,
            'harga_ansuransi' => 2500,
            'masa_ansuransi' => 3,
        ]);


        gk_tiket_pendaki::create([
            'id_paket_tiket' => 2,
            'kategori_pendaki' => 'wni',
            'harga_masuk_wk' => 7500,
            'harga_masuk_wd' => 5000,
            'harga_kemah' => 5000,
            'harga_traking' => 20000,
            'harga_ansuransi' => 2500,
            'masa_ansuransi' => 3,
        ]);
        gk_tiket_pendaki::create([
            'id_paket_tiket' => 2,
            'kategori_pendaki' => 'wna',
            'harga_masuk_wk' => 150000,
            'harga_masuk_wd' => 150000,
            'harga_kemah' => 5000,
            'harga_traking' => 20000,
            'harga_ansuransi' => 2500,
            'masa_ansuransi' => 3,
        ]);
    }
}
