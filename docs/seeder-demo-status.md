# Seeder peringatan dan seluruh status tiket

Jalankan dari direktori backend:

```sh
php artisan db:seed
```

`DatabaseSeeder` kini menjalankan `BookingStatusSeeder` setelah tiket aktif dan
selesai, lalu `EmergencyMessageSeeder`. Tidak perlu menjalankan class tambahan
secara manual untuk pemakaian lokal sehari-hari.

## Peringatan dini

Jika tabel `gk_emergency_messages` masih kosong, dibuat tepat dua pesan
`admin_broadcast`, severity `medium` dan `high`, status `active`, berlabel DEMO.
Jika sudah ada pesan apa pun, tidak ada pesan yang ditambah atau diubah.
Tidak memanggil event broadcast. Pesan tetap dapat terlihat melalui daftar/API
peringatan aktif; jangan menganggapnya laporan kondisi lapangan.

## Tiket

Status dasar menurut model: 0 Menunggu Persetujuan, 1 Menyetujui SNK,
2 Mengisi Formulir, 3 Menunggu Pembayaran, 4 Sudah Bayar,
5 Konfirmasi Pendakian, 6 Check-in/sedang mendaki, 7 Check-out,
8 Selesai, 9 Dibatalkan.

Status tampilan turunan dibuat melalui status dasar, tanggal, dan riwayat anggota:

| Tampilan | Nilai database | Kondisi contoh |
| --- | --- | --- |
| 40 Kadarluarsa | 4 | Tanggal keluar sudah lewat |
| 50 Batal Mendaki | 5 | Seluruh anggota punya satu riwayat batal, jadwal masih akan datang |
| 51 Perlu Konfirmasi Check-in | 4 | Tanggal masuk hari ini |
| 61 Perlu Konfirmasi Check-out | 6 | Tanggal keluar sudah lewat |

Seeder mengecek status tampilan yang sudah tersedia, lalu menambahkan contoh
yang belum tercakup. Menggunakan akun/anggota demo Satria, Mukhtada, Opi dari
BookingSeeder, paket dan gate yang sama, kode tiket unik, tagihan dihitung ulang
untuk tanggal masing-masing. Tiket berbayar tambahan memiliki pembayaran
simulasi `success`; tidak menghubungi payment gateway. Tiket dibatalkan menyimpan
pelaku, waktu dan alasan pembatalan. Tiket check-in/check-out mempunyai waktu dan
riwayat anggota yang sesuai.

Pengulangan tidak menggandakan contoh pada kondisi/tanggal yang sama. Contoh
yang sudah berubah karena diproses pengguna dipertahankan. Status berbasis tanggal
dapat berubah seiring waktu; seeder tidak memundurkan status atau menggeser tanggal
tiket lama untuk memaksakan tampilan. Jika identitas contoh untuk status yang
hilang sudah ada, seeder memberi peringatan dan mempertahankannya.

## Production tanpa menghapus data

Backup/export database dahulu dan pastikan koneksi `.env` menunjuk database tujuan
yang benar. Jangan gunakan `migrate:fresh`, `migrate:refresh`, `db:wipe`, atau
truncate untuk memperbarui data production. Tidak ada migrasi baru dalam perubahan ini.

Alur `db:seed` keseluruhan lama bukan alur yang hanya menambahkan data:
`permisionSeeder` menghapus seluruh role/permission lalu membuat ulang, dan
`UserSampelSeeder` memperbarui nama/email akun contoh. Memilih menjalankan semua
seeder berarti menerima efek ini; backup dibutuhkan untuk pemulihan.

Untuk hanya memasukkan dua jenis data tambahan, dengan akun/destinasi/paket demo
dan schema yang sudah tersedia, gunakan:

```sh
php artisan db:seed --class=BookingStatusSeeder --force
php artisan db:seed --class=EmergencyMessageSeeder --force
```

Ini tidak menjalankan ulang seeder role/permission maupun akun. `--force` hanya
melewati prompt lingkungan production, bukan membuat seeder otomatis aman.
Contoh tiket tetap masuk laporan dan pesan DEMO aktif dapat tampil kepada pengguna;
pakai staging untuk data uji jika production digunakan publik.

Jika memang ingin seluruh seeder di production, periksa efek setiap seeder dan
backup dahulu, kemudian `php artisan db:seed --force`. Kode ini tidak menjalankan
perintah itu otomatis, tidak membuat ZIP, dan tidak mengunggah/deploy apa pun.
