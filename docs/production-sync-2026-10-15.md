# Penyamaan source lokal dengan production — 15 Oktober 2026

Source lokal telah disamakan dengan snapshot production TNKS yang diverifikasi pada audit tanggal yang sama. Source/data server production tidak diubah.

## Hasil verifikasi

| Pemeriksaan | Hasil |
|---|---|
| Source dalam manifest audit | **277/277 cocok**, SHA-256 setelah normalisasi akhir baris LF; tidak ada perbedaan. |
| Route runtime | **189/189 cocok**, berdasarkan method, URI, action, middleware; tidak ada tambahan/kekurangan. |
| Dependency PHP lokal | **158/158 sesuai composer.lock** termasuk paket pengembangan; Laravel v11.51.0, reCAPTCHA v6.1.0 dan Reverb v1.10.1. |
| Sintaks 12 file yang disalin | Seluruhnya lolos pemeriksaan PHP. |
| Suite lama dengan harness data uji terisolasi | **40 tes, 116 assertions**, tanpa kegagalan atau skip. |
| Tes tambahan perilaku production | **7 tes, 21 assertions**, tanpa kegagalan atau skip. |

Sebanyak 11 file diganti dengan salinan production yang hash-nya sudah diverifikasi, dan `config/midtrans.php` ditambahkan. Perubahan mencakup daftar booking yang diikuti anggota, resolver pendaki berdasarkan biodata pada tracking/emergency/SOS, pembatasan gate pada QR/manual, konfigurasi Midtrans, kapitalisasi relasi destinasi dan default panjang string schema.

Seluruh source route sudah cocok sebelum penyamaan. Route reCAPTCHA yang sebelumnya tidak muncul berasal dari dependency lokal yang belum lengkap. Instalasi mengikuti **composer.lock yang ada**, tanpa memperbarui composer.json/lock atau menambahkan route buatan.

Perubahan lokal pengguna pada dua controller lain dan seeder dipertahankan. File tersebut sudah cocok dengan manifest production; tidak ditimpa atau dimasukkan ke commit penyamaan ini. Satu file lokal tambahan dalam direktori cakupan, `database/.gitignore`, adalah metadata Git, bukan source aplikasi pembanding.

## Pengujian dan batasnya

Pengujian menggunakan SQLite `:memory:` dan driver PDO SQLite yang diaktifkan hanya untuk proses uji. Database kerja tidak direset, di-seed atau dimigrasi. Broadcast dinonaktifkan, email memakai penyimpanan array, dan tes tambahan memalsukan event broadcast; tidak ada SOS, email atau transaksi production terkirim.

Eksekusi awal `php artisan test --compact` pada database memori kosong mengalami **7 error fixture**: beberapa tes lama membaca `User::first()` atau akun admin tanpa menyiapkan tabel/data sendiri. Untuk memeriksa aplikasi tanpa mengubah source/test asli, harness terpisah di folder audit menambahkan `RefreshDatabase` serta akun admin sintetis per tes. Dengan harness ini, 40 tes lama lolos. Ini bukan klaim bahwa runner standar tanpa persiapan fixture telah diperbaiki.

Tujuh tes tambahan memverifikasi:

1. GPS anggota tersimpan dengan identitas pendaki anggota sendiri.
2. Anggota dapat mengirim peringatan untuk pendakian aktifnya.
3. Anggota dapat membuat SOS untuk pendakian aktifnya.
4. Anggota dapat mengirim laporan potensi bencana.
5. Daftar tiket anggota memuat booking yang diikutinya.
6. QR dan manual menolak pos dari gate lain tanpa membuat check-in.
7. Akun tanpa biodata ditolak ketika mengirim GPS/peringatan/SOS/laporan.

Kedua eksekusi terisolasi masih memunculkan **4 jenis deprecation** pada PHP 8.5 dari source/dependency yang dipertahankan sesuai production. Composer juga melaporkan namespace/path lama yang tidak sesuai PSR-4 dan status paket reCAPTCHA abandoned. Hal-hal tersebut dicatat, bukan diperbaiki dengan perubahan source yang menyimpang dari snapshot production. Spasi akhir baris yang sudah ada di production juga dipertahankan agar hash tetap cocok.

## Konfigurasi lokal

`MidtransController` kini mengambil nilai dari `config/midtrans.php`, sama dengan production. Kunci **sandbox lokal yang sebelumnya tertulis di source** dipindahkan ke `.env` lokal sebagai `MIDTRANS_SERVER_KEY`, dan `MIDTRANS_IS_PRODUCTION=false` dipertahankan. Nilai kunci tidak dicetak, tidak disalin ke laporan, dan tidak di-commit. Kredensial production tidak dibaca atau disalin. Konfigurasi client key tidak dipaksakan; tidak ada transaksi pembayaran yang diuji.

Cache konfigurasi/bootstrap lokal dibersihkan. Pemeriksaan paket dilakukan setelah package discovery, sehingga route vendor aktif sesuai paket terkunci.

## Arti kesamaan 100%

Kesamaan yang dibuktikan adalah **seluruh 277 file dalam manifest source audit**, setelah normalisasi LF, dan metadata **189 route** terhadap snapshot production 15 Oktober 2026. Ini tidak membuktikan seluruh isi komputer/server identik. `.env`, database, file upload, storage, seluruh asset publik, konfigurasi hosting, cron/worker, runtime PHP/OS dan isi vendor production aktual tidak diinventarisasi byte-per-byte. Versi paket lokal terbukti sesuai lock file production, bukan hasil hash seluruh vendor server.

Tes audit ini bukan pengganti acceptance testing, UAT, BBT atau unit test final penelitian. Laporan perbandingan sebelumnya tetap merupakan riwayat kondisi **sebelum penyamaan**. Dokumentasi/koleksi Postman lama yang menyebut perilaku lokal lama harus dibaca dalam konteks tanggal snapshot tersebut.

Bukti lengkap berada di folder skripsi `audit-context/production-comparison-2026-10-15/`: `local-sync-verification.json`, `local-sync-route-verification.json`, `local-sync-dependency-verification.json`, `local-sync-php-lint.txt`, `local-sync-tests.txt`, `local-sync-isolated-tests.xml`, `local-sync-parity-tests.xml` dan log pendamping. Harness/tes audit tersimpan terpisah dari source aplikasi agar source yang dibandingkan tetap identik.
