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

        destinasi::create([
            'nama' => 'Gunung Kerinci',
            'status' => 2,
            'statusGunung' => 0,
            'kategori' => 'gunung',
            'lokasi' => 'Gunung Kerinci terletak di Provinsi Jambi yang berbatasan dengan provinsi Sumatera Barat, di Pegunungan Bukit Barisan, dekat pantai barat, dan terletak sekitar 130 km sebelah selatan Padang',
            'detail' => '<h5><strong style="color: rgb(33, 37, 41);">Wisata Gunung Kerinci: Keindahan Alam Tertinggi di Sumatera</strong></h5><p><span style="color: rgb(33, 37, 41);">Gunung Kerinci, yang terletak di Provinsi Jambi, adalah gunung tertinggi di Sumatera dan gunung berapi tertinggi di Indonesia dengan ketinggian mencapai 3.805 meter di atas permukaan laut. Gunung ini berada dalam kawasan Taman Nasional Kerinci Seblat (TNKS), yang merupakan salah satu situs warisan dunia UNESCO. Destinasi wisata ini terkenal dengan panorama alamnya yang memukau, keanekaragaman hayati, dan tantangan pendakiannya.</span></p><h5><strong style="color: var(--bs-heading-color);">Keindahan Alam dan Pemandangan</strong></h5><p><span style="color: rgb(33, 37, 41);">Gunung Kerinci menawarkan pemandangan yang luar biasa indah, mulai dari lembah hijau yang subur, perkebunan teh Kayu Aro yang membentang luas, hingga hamparan hutan tropis yang masih sangat alami. Dari puncaknya, pendaki bisa menikmati panorama matahari terbit dengan latar belakang Samudra Hindia di sisi barat dan Danau Gunung Tujuh yang eksotis di sisi timur.</span></p><h5><strong style="color: var(--bs-heading-color);">Aktivitas Pendakian</strong></h5><p><span style="color: rgb(33, 37, 41);">Pendakian Gunung Kerinci adalah daya tarik utama bagi para pecinta alam dan petualangan. Rute pendakian biasanya dimulai dari Desa Kersik Tuo. Pendakian membutuhkan waktu sekitar 2-3 hari tergantung pada kondisi cuaca dan stamina pendaki. Rute ini memberikan pengalaman melewati hutan tropis yang lebat, lengkap dengan flora dan fauna endemik seperti harimau Sumatera dan burung rangkong. Tips Pendakian:</span></p><ol><li data-list="bullet" class="ql-align-justify"><span class="ql-ui" contenteditable="false"></span><span style="color: rgb(33, 37, 41);">Pastikan kondisi fisik dalam keadaan prima.</span></li><li data-list="bullet" class="ql-align-justify"><span class="ql-ui" contenteditable="false"></span><span style="color: rgb(33, 37, 41);">Gunakan perlengkapan pendakian yang memadai, seperti jaket tebal, sepatu gunung, dan lampu senter.</span></li><li data-list="bullet" class="ql-align-justify"><span class="ql-ui" contenteditable="false"></span><span style="color: rgb(33, 37, 41);">Ikuti panduan dari pemandu lokal untuk menjaga keselamatan.</span></li></ol><h5><strong style="color: var(--bs-heading-color);">Keanekaragaman Hayati</strong></h5><p><span style="color: rgb(33, 37, 41);">Sebagai bagian dari TNKS, Gunung Kerinci memiliki kekayaan flora dan fauna yang luar biasa. Kawasan ini merupakan habitat bagi berbagai spesies langka, termasuk harimau Sumatera, badak Sumatera, serta beragam jenis burung dan tanaman endemik seperti bunga Rafflesia dan bunga bangkai (Amorphophallus titanum).</span></p><h5><strong style="color: var(--bs-heading-color);">Destinasi Wisata Sekitar</strong></h5><p><span style="color: rgb(33, 37, 41);">Selain mendaki Gunung Kerinci, pengunjung juga bisa mengeksplorasi berbagai destinasi wisata lain di sekitarnya, seperti:</span></p><ol><li data-list="bullet" class="ql-align-justify"><span class="ql-ui" contenteditable="false"></span><span style="color: rgb(33, 37, 41);">Danau Gunung Tujuh: Danau vulkanik tertinggi di Asia Tenggara.</span></li><li data-list="bullet" class="ql-align-justify"><span class="ql-ui" contenteditable="false"></span><span style="color: rgb(33, 37, 41);">Perkebunan Teh Kayu Aro: Salah satu perkebunan teh tertua dan tertinggi di dunia.</span></li><li data-list="bullet" class="ql-align-justify"><span class="ql-ui" contenteditable="false"></span><span style="color: rgb(33, 37, 41);">Air Terjun Telun Berasap: Air terjun megah yang dikelilingi hutan tropis.</span></li></ol><h5><strong style="color: var(--bs-heading-color);">Akses Menuju Gunung Kerinci</strong></h5><p><span style="color: rgb(33, 37, 41);">Untuk mencapai Gunung Kerinci, pengunjung bisa terbang ke Bandara Sultan Thaha di Jambi atau Minangkabau di Padang, lalu melanjutkan perjalanan darat ke Desa Kersik Tuo. Perjalanan ini memakan waktu sekitar 6-8 jam, tetapi pemandangan sepanjang jalan cukup menakjubkan.</span></p><p><br></p>',
        ]);

        Destinasi::create([
            'nama' => 'Danau Kaco',
            'status' => 1,
            'statusGunung' => 0,
            'kategori' => 'gunung',
            'lokasi' => 'Danau Kaco merupakan danau yang terletak di kabupaten Kerinci, Jambi, tepatnya di desa Lempur, kecamatan Gunung Raya',
            'detail' => 'Danau Kaco adalah salah satu destinasi wisata yang terkenal di Indonesia, terletak di Desa Lempur, Kecamatan Gunung Raya, Kabupaten Kerinci, Jambi. Danau ini merupakan bagian dari Taman Nasional Kerinci Seblat, yang telah diakui oleh UNESCO sebagai situs warisan dunia karena memiliki keanekaragaman hayati yang sangat tinggi. Dengan luas sekitar 90 meter persegi, merupakan danau yang cukup kecil. Namun, keindahan dan keunikan danau ini membuatnya menjadi salah satu destinasi wisata yang banyak dikunjungi. Kedalaman danau ini masih belum diketahui secara pasti, namun menurut para pengunjung yang pernah datang ke sana, kedalamannya cukup dalam.',
        ]);

        Destinasi::create([
            'nama' => 'Danau Gunung Tujuh',
            'status' => 1,
            'statusGunung' => 0,
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
