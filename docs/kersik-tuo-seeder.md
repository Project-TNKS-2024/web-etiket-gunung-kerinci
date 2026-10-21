# Seeder jalur Kersik Tuo

## Gambar peta WebP

Sepuluh peta pos Kersik Tuo tersedia sebagai WebP kualitas 88 dengan resolusi asli 1536 × 2752. Total ukuran turun dari 20.584.819 menjadi 987.476 byte (95,2% lebih kecil); Pondok Panorama turun dari 2.062.122 menjadi 84.278 byte. PNG asli tetap tersedia untuk URL lama. Solok Selatan belum memiliki gambar peta dalam backend, sehingga tidak dibuat gambar pengganti.

Seeder memakai path WebP. Untuk database lama yang masih menyimpan path PNG di `images/trail-maps/`, accessor `map_image_url` otomatis memakai file WebP pendamping jika tersedia. Path peta lain tetap digunakan seperti sebelumnya. Penerapan ke production cukup menyertakan perubahan model dan sepuluh file WebP; tidak perlu menjalankan ulang seeder, migrasi, atau mengubah ID/koordinat/kehadiran. Perubahan seeder juga disertakan untuk pemasangan baru. Verifikasi URL dari API v1/v2 dan respons gambar `Content-Type: image/webp` setelah deployment. Perubahan ini baru diterapkan dan diperiksa secara lokal.

Verifikasi 21 Oktober 2026: 13 pengujian peta/seeder selesai tanpa kegagalan (412 assertions, dengan peringatan deprecation PHP 8.5); build Vite berhasil. Sepuluh URL peta database lokal memakai WebP, dan gambar Pondok Panorama mengembalikan HTTP 200 dengan `Content-Type: image/webp`, panjang 84.278 byte. Pengujian mobile tetap 19 dan semuanya lulus. Suite backend penuh belum hijau: 45 pengujian gagal dengan constraint `gk_bookings_id_tiket_foreign` pada penyusunan data uji tiket, di luar cakupan optimasi gambar. Seluruh pengujian backend dijalankan pada database MySQL sementara terpisah, kemudian database uji dihapus.

Jalur aktif mengikuti sepuluh peta posisi: 10 titik Kersik Tuo termasuk titik awal, ditambah 4 titik Solok Selatan. `urutan` mengatur posisi pada jalur; `id` adalah identitas database yang dipertahankan dan tidak harus dimulai dari 1 atau berurutan.

| Urutan | Titik | Elevasi (mdpl) | Latitude perkiraan | Longitude perkiraan |
|---:|---|---:|---:|---:|
| 1 | Kersik Tuo | 1605 | -1.7650 | 101.2650 |
| 2 | Pintu Rimba | 1809 | -1.7483 | 101.2594 |
| 3 | Bangku Panjang | 1890 | -1.7407 | 101.2601 |
| 4 | Batu Lumut | 2010 | -1.7348 | 101.2603 |
| 5 | Pondok Panorama | 2367 | -1.7290 | 101.2622 |
| 6 | Shelter 1 | 2505 | -1.7204 | 101.2636 |
| 7 | Shelter 2 | 3056 | -1.7096 | 101.2675 |
| 8 | Shelter 3 | 3290 | -1.7056 | 101.2673 |
| 9 | Tugu Yudha | 3673 | -1.6992 | 101.2642 |
| 10 | Puncak Kerinci | 3805 | -1.6974 | 101.2661 |

Koordinat adalah **perkiraan untuk pengembangan**, bukan survei GPS atau titik navigasi lapangan. Acuan bentuk jalur adalah [peta Gunung Bagging/Indogis edisi 2020.1](https://www.gunungbagging.com/wp-content/maps/peta-jalur-pendakian-gunung-kerinci.pdf), dibaca secara pendekatan dari grid peta. Titik Kersik Tuo merupakan perkiraan area pendekatan/basecamp, bukan titik trailhead 1.755 m pada peta; Batu Gantung diinterpolasi antara Shelter 3 dan Tugu Yudha dan tidak diberi waypoint tersendiri pada peta acuan. Elevasi menggunakan angka penulis, bukan klaim seluruh angka tersebut berasal dari peta. Beberapa elevasi berbeda dari peta, termasuk Pondok Panorama (2.211 m pada peta), sehingga koordinat/elevasi belum terverifikasi sebagai satu set pengukuran lapangan. Radius 150 m (200 m untuk puncak) adalah konfigurasi lama yang dipertahankan, bukan ukuran ketelitian koordinat perkiraan.

Saat dijalankan ulang, seeder memperbarui nama lama dan urutan, mempertahankan ID/QR/status pos lama, dan memperbarui hanya koordinat contoh lama yang dikenali. Ketinggian yang sudah terisi serta gambar khusus admin dipertahankan. Ketinggian/peta kosong dilengkapi; hanya path PNG bawaan yang ditingkatkan ke WebP bawaan. Koordinat lain hasil edit admin tetap disimpan. Pemetaan alias lama mencakup Pos 1 ke Bangku Panjang, Pos 2 ke Batu Lumut, dan label campuran Pos 3 (Shelter 2) ke Pondok Panorama; alias bercampur ini mengikuti penomoran Pos 3, bukan dianggap sebagai Shelter 2 baru. Bila nama lama dan baru sama-sama sudah ada, transaksi dibatalkan agar riwayat tidak dihapus atau digabung diam-diam.

Bayangan Shelter 2 dan Batu Gantung tidak masuk jalur aktif. Jika row tersebut sudah ada di gate Kersik Tuo, seeder menonaktifkannya tanpa menghapus ID, QR atau log kehadiran. Gate dicari berdasarkan nama, bukan asumsi ID 1/2. Data dan urutan pos Solok Selatan tetap seperti sebelumnya.

## ID dan QR stabil saat seeding ulang

Pos dicari berdasarkan nama gate dan nama pos/alias lama. Pos yang ditemukan diperbarui tanpa dibuat ulang; pos baru dibuat sekali. ID serta nilai QR lama dipertahankan. QR default pos baru memakai `TNKS_CHECKPOINT:<id>` dari ID aktual database. Jangan mereset auto-increment, menghapus pos, atau memindahkan primary key agar angka terlihat kecil. `db:seed` berbeda dari `migrate:fresh --seed`, yang menghapus tabel beserta riwayatnya.

Generator dalam folder `qr-checkpoint` membaca pos aktif dan nilai QR dari database lokal, mengenali jalur berdasarkan nama gate, dan membuat ulang daftar serta PNG. ID gate/pos tidak di-hardcode. QR pos yang dinonaktifkan dikeluarkan dari folder cetak, sementara catatan database tetap dipertahankan. Setiap PNG diuji pindai agar hasilnya sama dengan nilai QR database.

Untuk menerapkan ke database lokal yang sudah benar koneksinya, jalankan hanya seeder ini: `php artisan db:seed --class=TrailPostSeeder`. Seeder memerlukan gate Kersik Tuo dan Solok Selatan sudah tersedia. Hindari menjalankan ulang seluruh DatabaseSeeder hanya untuk memperbarui pos, karena seeder utama juga menjalankan proses data lain. Pada 21 Oktober 2026, seeder pos dijalankan dua kali pada database lokal: identitas 14 pos aktif, QR, dan seluruh catatan absen tetap sama. Generator menghasilkan 14 PNG dan seluruhnya berhasil diuji pindai. Pengulangan seluruh DatabaseSeeder juga diperiksa melalui pengujian pada database MySQL terpisah.

## Tiket demo dan tanggal seeding

Tiket aktif utama baru dimulai pada hari seeding menurut zona waktu aplikasi (WIB), dengan tanggal keluar dua hari sesudahnya: tiga hari kalender. Misalnya seeding 21 Oktober 2026 menghasilkan 21–23 Oktober. Tanggal masuk dan keluar tiket yang sudah ada tidak digeser ketika seeding ulang. Jika melewati masa tiket, tiket yang sama menjadi tampilan status 61; tidak dibuat booking aktif kedua untuk pendaki yang sama.

`BookingStatusSeeder` tidak membuat contoh status 6/61, karena keduanya berasal dari tiket aktif utama. Pada database baru, cakupan awal menjadi 13 tiket demo, dengan tepat satu booking aktif untuk Satria, Mukhtada, dan Opi. Contoh tiket selesai baru memakai tanggal empat hingga dua hari sebelum seeding (misalnya 17–19 Oktober untuk seeding 21 Oktober), check-in pukul 08.00 hari masuk dan check-out pukul 16.00 hari keluar. Tagihan dihitung kembali sesuai tanggal contoh selesai. Contoh status lain mempertahankan aturan tanggal sebelumnya.

Jika sumber demo aktif tidak tersedia, seeder status melewati proses dengan pesan informasi, sama seperti seeder tiket selesai. Booking operasional tidak dijadikan sumber pengganti. Booking demo lama yang sudah terbuat, termasuk contoh aktif ganda versi lama, tidak dihapus, dibatalkan, atau diubah tanggalnya secara otomatis. Penanganan data lama perlu memeriksa riwayatnya tersendiri; perubahan ini memperbaiki pembuatan data baru, bukan mereset data pengguna.

Verifikasi perbaikan ini pada database MySQL sementara: 21 pengujian terkait seeder/relasi/API pos lulus (479 assertions; ada peringatan deprecation PHP 8.5). Pengujian mencakup seeding ulang lima hari kemudian, sumber demo hilang/sudah selesai, ketinggian/gambar khusus admin, dan tanggal tiket selesai. Database lokal pengguna tidak diseed ulang pada perubahan ini.

Perubahan ini disengaja berbeda dari snapshot production yang sebelumnya disamakan. Tidak ada deployment atau push GitHub pada pekerjaan ini.
