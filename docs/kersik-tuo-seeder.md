# Seeder jalur Kersik Tuo

Nama, urutan dan elevasi mengikuti daftar terbaru yang dipilih penulis pada 15 Oktober 2026. Kersik Tuo dihitung sebagai titik pertama, sehingga ada 11 titik termasuk titik awal.

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
| 9 | Batu Gantung | 3528 | -1.7029 | 101.2652 |
| 10 | Tugu Yudha | 3673 | -1.6992 | 101.2642 |
| 11 | Puncak Kerinci | 3805 | -1.6974 | 101.2661 |

Koordinat adalah **perkiraan untuk pengembangan**, bukan survei GPS atau titik navigasi lapangan. Acuan bentuk jalur adalah [peta Gunung Bagging/Indogis edisi 2020.1](https://www.gunungbagging.com/wp-content/maps/peta-jalur-pendakian-gunung-kerinci.pdf), dibaca secara pendekatan dari grid peta. Titik Kersik Tuo merupakan perkiraan area pendekatan/basecamp, bukan titik trailhead 1.755 m pada peta; Batu Gantung diinterpolasi antara Shelter 3 dan Tugu Yudha dan tidak diberi waypoint tersendiri pada peta acuan. Elevasi menggunakan angka penulis, bukan klaim seluruh angka tersebut berasal dari peta. Beberapa elevasi berbeda dari peta, termasuk Pondok Panorama (2.211 m pada peta), sehingga koordinat/elevasi belum terverifikasi sebagai satu set pengukuran lapangan. Radius 150 m (200 m untuk puncak) adalah konfigurasi lama yang dipertahankan, bukan ukuran ketelitian koordinat perkiraan.

Saat dijalankan ulang, seeder memperbarui nama lama dan urutan serta elevasi, mempertahankan ID/QR/status pos lama, dan memperbarui hanya koordinat contoh lama yang dikenali. Koordinat lain hasil edit admin tetap disimpan. Pemetaan alias lama mencakup Pos 1 ke Bangku Panjang, Pos 2 ke Batu Lumut, dan label campuran Pos 3 (Shelter 2) ke Pondok Panorama; alias bercampur ini mengikuti penomoran Pos 3, bukan dianggap sebagai Shelter 2 baru. Bila nama lama dan baru sama-sama sudah ada, transaksi dibatalkan agar riwayat tidak dihapus atau digabung diam-diam.

Bayangan Shelter 2 tidak dimasukkan dalam daftar terbaru. Jika row tersebut sudah ada di gate Kersik Tuo, seeder menonaktifkannya tanpa menghapus ID, QR atau log kehadiran. Gate dicari berdasarkan nama, bukan asumsi ID 1/2. Data dan urutan pos Solok Selatan tetap seperti sebelumnya.

Untuk menerapkan ke database lokal yang sudah benar koneksinya, jalankan hanya seeder ini: `php artisan db:seed --class=TrailPostSeeder`. Seeder memerlukan gate Kersik Tuo dan Solok Selatan sudah tersedia. Hindari menjalankan ulang seluruh DatabaseSeeder hanya untuk memperbarui pos, karena seeder utama juga menjalankan proses data lain. Pada pekerjaan ini seeder hanya diuji pada database SQLite memori; database kerja dan production belum diubah.

Perubahan ini disengaja berbeda dari snapshot production yang sebelumnya disamakan. Tidak ada deployment atau push GitHub pada pekerjaan ini.
