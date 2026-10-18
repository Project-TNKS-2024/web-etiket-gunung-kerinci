# Tiket mobile dan administrasi — 18 Oktober 2026

## Perubahan

- Mobile mengikuti frame Figma 1963:9022, 1963:9468, dan 1353:9652 pada file `2EsUevHudLtxUICJwEf5AW`: dua tab Draft/Tiket Saya, kartu pembelian, badge Aktif/Selesai, kode pemesanan, pemberitahuan, keadaan kosong, dan tombol pesan tiket. Aset ikon/ilustrasi diambil dari Figma.
- Status 0–3 dan 9 berada di Draft; 4–8 berada di Tiket Saya. Status 7–8 selesai mendaki. Biaya tambahan belum lunas tetap diberi peringatan. Kartu tiket selesai pada Pelacakan Jejak membuka detail tiket yang sudah tersedia.
- Tenggat pembayaran dan alasan pembatalan berasal dari backend. Bukti pending menampilkan menunggu verifikasi. Refresh mempertahankan data terakhir dan menyediakan coba lagi ketika gagal.
- Pembatalan sebelum pembayaran dikonfirmasi menyimpan status 9, alasan, waktu, pelaku, anggota, dan bukti pembayaran. Admin memiliki filter Draft/Dibatalkan, formulir alasan, serta detail riwayat. Pesanan batal tidak bisa dilanjutkan atau dikonfirmasi; tidak dihitung dalam kuota/statistik tiket terbit. Kedaluwarsa di alur web juga mempertahankan bukti pembayaran.
- Peringatan lewat jadwal turun dan tagihan tambahan memakai ringkasan overstay yang sudah ada; tidak ditambahkan layanan push atau scheduler baru.

## Verifikasi

- Android: `gradlew.bat :app:assembleDebug :app:testDebugUnitTest --offline` berhasil; 88 test yang sudah ada, tanpa kegagalan/error. Tidak menambahkan unit test baru.
- Backend: 34 test terkait tiket, checkout, keamanan admin, validasi API, kalkulator overstay, dan relasi seed berhasil (163 assertions). Ada empat deprecation dari suite/dependency yang sudah ada.
- Suite backend penuh: 120 berhasil dan lima error pada SOSApiTest karena fixture SQLite tidak memiliki tabel users. Lima error tersebut belum diselesaikan dalam perubahan tiket ini.
- Migration `86_gk_bookings_add_cancellation_history` berhasil dijalankan pada database lokal. Lingkungan lain perlu menjalankan `php artisan migrate` sebelum memakai kode baru.
- Pemeriksaan API menggunakan database QA terpisah: respons mytiket, tenggat, pembatalan pemilik, anggota tetap tersimpan, pembatalan berulang, penolakan pembatalan tiket aktif, penolakan melanjutkan pesanan batal, dan detail tiket selesai berhasil.
- Browser admin pada database QA: daftar/filter batal, pembatalan dengan alasan, waktu/pelaku, anggota tetap tersimpan, dan pembayaran batal tanpa tombol verifikasi berhasil. Blade cache dan pemeriksaan sintaks/diff berhasil.
- Bukti tampilan: [admin setelah pembatalan](ticket-admin-cancelled.png).

## Batas pemeriksaan

Seeder tambahan `CompletedBookingSeeder` dipanggil sesudah `BookingSeeder` dalam `DatabaseSeeder`. Seeder menyalin tiket demo aktif Satria beserta tiga anggota, gate, tanggal, dan biaya ke tiket demo status 8, dengan kode QR berbeda dan riwayat checkout seluruh anggota. Tiket aktif tidak diubah; pemanggilan ulang tidak menambah duplikat. Sudah dijalankan pada database lokal. Endpoint detail tiket selesai mengembalikan HTTP 200. Enam test terkait seeder/daftar tiket yang sudah ada berhasil (49 assertions); tanpa test baru.

Belum diverifikasi secara visual pada emulator/HP Android. Pemeriksaan ukuran layar/font besar, perpindahan layar, dan kondisi jaringan nyata tetap perlu dilakukan pada perangkat. Dashboard QA SQLite memiliki query MONTH khusus MySQL yang sudah ada; pemeriksaan browser dilakukan langsung pada halaman booking.
