# Verifikasi kartu Beranda — 18 Oktober 2026

Mobile mempertahankan susunan tiga bagian Figma 1501:8547, memakai ikon gunung asli 12×12, font Poppins/Plus Jakarta, radius 8dp, padding 20/8dp, tinggi mengikuti isi, dan Flow untuk pembungkusan kelompok. Slot status tidak lagi diganti ikon cuaca. Ketukan kartu membuka penjelasan X sedang mendaki/Y total kunjungan seluruh riwayat, waktu pengamatan cuaca, dan waktu ringkasan. Nilai tidak tersedia berbeda dari nol; refresh mempertahankan data lama dan menyediakan retry.

Backend `/api/beranda` menambahkan ringkasan jumlah, status gunung, serta waktu pengamatan/ringkasan tanpa menghapus field lama. Relasi status/biodata pendaki dimuat bersama untuk menghindari query per anggota. Cuaca memakai bahasa Indonesia, batas waktu, cache respons berhasil 5 menit, dan key lingkungan. URL/key tidak dicatat dalam log baru. Tidak ada migration, library, endpoint, atau unit test baru.

## Hasil pemeriksaan

- Android `:app:assembleDebug :app:testDebugUnitTest --offline`: berhasil; 88 test yang sudah ada, 0 failure/error. XML valid; ikon Figma 359 byte, 12×12, dipakai dalam ImageView 12dp.
- Backend suite penuh: 125 test, 708 assertions, 5 error pada fixture SOSApiTest yang belum membuat tabel users pada SQLite; sama seperti pemeriksaan sebelumnya. Ada 4 deprecation.
- Pemeriksaan endpoint melalui HTTP kernel/token pada database QA terpisah: respons 200, pemetaan cuaca/status, kompatibilitas field lama, cakupan angka, cache provider, cuaca gagal tanpa kehilangan angka/status, nol valid, dan konfigurasi cuaca tidak tersedia berhasil. Token sementara dicabut; perubahan data QA untuk skenario kosong di-rollback.
- WeatherAPI sebenarnya berhasil mengembalikan data suhu setelah PHP menggunakan CA bundle dari root tepercaya Windows. Bundle disimpan privat dan ditunjuk melalui WEATHER_CA_BUNDLE lokal; verifikasi SSL tetap aktif. Key lokal dipindahkan ke environment, tidak ikut commit.
- PHP lint, route:list, dan git diff --check berhasil.

## Batas pemeriksaan

Belum memasang APK baru atau memeriksa tampilan langsung di HP/emulator. Pemeriksaan screenshot terhadap Figma, font besar, layar sempit, TalkBack, dan ketukan retry dengan jaringan nyata masih diperlukan pada perangkat. Jangan menganggap build sebagai bukti kesamaan visual sempurna.

Key cuaca yang sebelumnya pernah berada di kode/log perlu dirotasi oleh pemilik akun provider. Rotasi tidak dilakukan karena akses akun provider tidak tersedia dalam tugas ini. Lingkungan lain perlu mengisi WEATHER_API_KEY; WEATHER_CA_BUNDLE hanya bila trust store PHP memerlukannya.
