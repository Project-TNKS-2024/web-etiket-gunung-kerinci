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
            'kategori' => 'gunung',
            'lokasi' => 'Gunung Kerinci terletak di Provinsi Jambi yang berbatasan dengan provinsi Sumatera Barat, di Pegunungan Bukit Barisan, dekat pantai barat, dan terletak sekitar 130 km sebelah selatan Padang',
            'detail' => 'Gunung Kerinci adalah gunung berapi tertinggi di Indonesia dengan ketinggian 3.805 meter di atas permukaan laut. Terletak di Provinsi Jambi, gunung ini menjadi bagian dari Taman Nasional Kerinci Seblat yang juga merupakan situs warisan dunia UNESCO. Gunung Kerinci populer di kalangan pendaki karena pemandangan puncaknya yang menakjubkan, dengan panorama lembah, hutan hujan tropis, dan bahkan pemandangan Samudera Hindia di kejauhan.Gunung Kerinci adalah gunung berapi tertinggi di Indonesia dengan ketinggian 3.805 meter di atas permukaan laut. Terletak di Provinsi Jambi, gunung ini menjadi bagian dari Taman Nasional Kerinci Seblat yang juga merupakan situs warisan dunia UNESCO. Gunung Kerinci populer di kalangan pendaki karena pemandangan puncaknya yang menakjubkan, dengan panorama lembah, hutan hujan tropis, dan bahkan pemandangan Samudera Hindia di kejauhan.',
        ]);

        Destinasi::create([
            'nama' => 'Danau Kaco',
            'status' => false,
            'kategori' => 'gunung',
            'lokasi' => 'Danau Kaco merupakan danau yang terletak di kabupaten Kerinci, Jambi, tepatnya di desa Lempur, kecamatan Gunung Raya',
            'detail' => 'Danau Kaco adalah salah satu destinasi wisata yang terkenal di Indonesia, terletak di Desa Lempur, Kecamatan Gunung Raya, Kabupaten Kerinci, Jambi. Danau ini merupakan bagian dari Taman Nasional Kerinci Seblat, yang telah diakui oleh UNESCO sebagai situs warisan dunia karena memiliki keanekaragaman hayati yang sangat tinggi. Dengan luas sekitar 90 meter persegi, merupakan danau yang cukup kecil. Namun, keindahan dan keunikan danau ini membuatnya menjadi salah satu destinasi wisata yang banyak dikunjungi. Kedalaman danau ini masih belum diketahui secara pasti, namun menurut para pengunjung yang pernah datang ke sana, kedalamannya cukup dalam.',
        ]);

        Destinasi::create([
            'nama' => 'Danau Gunung Tujuh',
            'status' => true,
            'kategori' => 'gunung',
            'lokasi' => 'Desa Pelompek, Kecamatan Ayu Aro, Kabupaten Kerinci',
            'detail' => 'Danau Gunung Tujuh merupakan danau yang terletak di desa Pelompek, kabupaten Kerinci, Jambi. Danau ini berada di kawasan Gunung Tujuh, sebuah gunung yang berada tepat di belakang Gunung Kerinci. Gunung Tujuh masih termasuk dalam wilayah Taman Nasional Kerinci Seblat yang merupakan Situs Warisan Dunia UNESCO. Seperti namanya, danau gunung tujuh dikelilingi tujuh puncak gunung di Jambi. Tidak heran kalau tempat ini menjadi destinasi wisata yang menawarkan banyak panorama keindahan.Bagi pendaki gunung, Kerinci mungkin menjadi tujuan utama karena gunung tersebut merupakan gunung aktif tertinggi di Indonesia (3805 mdpl), tetapi bagi wisatawan yang ingin sekadar menikmati keindahan alam Kabupaten Kerinci, Danau Gunung Tujuh bisa menjadi pertimbangan sebagai tujuan wisata.',
        ]);

        gk_gates::create([
            'nama' => 'Desa Kersik Tuo',
            'status' => true,
            'id_destinasi' => '1',
            'max_pendaki_hari' => 30,
            'min_pendaki_booking' => 2,
            'lokasi_maps' => '-',
            'lokasi' => 'Desa Kersik Tuo, Kecamatan Kayu Aro, Kabupaten Kerinci, Jambi',
            'detail' => '.',
        ]);

        gk_gates::create([
            'nama' => 'Solok Selatan',
            'status' => true,
            'id_destinasi' => '1',
            'max_pendaki_hari' => 46,
            'min_pendaki_booking' => 2,
            'lokasi_maps' => '-',
            'lokasi' => 'Jl.Raya Timbulun, Lubuk Gadang, Kec. Sangir, Kabupaten Solok Selatan, Sumatera Barat 27778',
            'detail' => 'Camping Ground Bangun Rejo di Kabupaten Solok Selatan, Sumatera Barat.',
        ]);
    }
}
