# Catatan anggota per pos — 18 Oktober 2026

Referensi Figma: node 1501:9760 pada APP-PENDAKIAN-GUNUNG. Field memiliki bingkai 8dp, tinggi minimal 50dp, padding 16/8dp, placeholder Tambahkan Catatan 14sp, dan aset plus Figma asli 24×24.

## Perilaku

- Field ditampilkan hanya ketika anggota belum check-in (toggle merah). Ketika hijau, field hilang dari tampilan; catatan tersimpan tetap ada di backend.
- Setelah teks berubah, tombol Simpan muncul. Maksimal 500 karakter; status menyimpan lokal pada field, request ganda dicegah. Simpan gagal mempertahankan isian, tanpa menganggapnya tersimpan atau memasukkannya ke antrean offline.
- Catatan terpisah dari bukti check-in. Menyimpan catatan tidak menambah kunjungan, mengubah toggle, fase, atau progres.
- Ketua yang sudah check-in pada pos/fase yang sama dapat mencatat untuk anggota lain. Anggota hanya dapat mencatat miliknya sendiri. Backend memeriksa keanggotaan booking aktif dan gate; penulisan setelah anggota sudah check-in ditolak.
- Catatan dipisahkan menurut booking, anggota, pos, dan fase naik/turun. Penyimpanan disertakan dalam cache progres sesi yang sudah ada.

## Backend

Migration 87 membuat tabel `gk_checkpoint_member_notes`, sudah diterapkan pada database lokal. Lingkungan lain harus menjalankan `php artisan migrate` sebelum memakai endpoint/progres baru.

`POST /api/tracking/v2/member-note` memakai Sanctum. Body: `pendaki_id` UUID, `post_id`, `phase` naik/turun, dan `note` nullable string maksimal 500 karakter. Nilai kosong mengosongkan isi catatan tanpa menghapus baris riwayat. Respons memakai format standar success/message/data. `GET /api/tracking/v2/progress/{booking_id}` menambahkan `member_notes` untuk booking yang memang dapat dibaca pengguna.

## Verifikasi

- Build Android dan 88 unit test yang sudah ada berhasil, tanpa tambahan unit test.
- Smoke API melalui HTTP kernel/token: bukti ketua di pos wajib, simpan catatan anggota merah, tidak membuat check-in, baca ulang melalui progress, batas 500 karakter, penolakan anggota asing, autentikasi, penolakan edit setelah hijau, dan catatan tetap disimpan setelah check-in berhasil diperiksa. Semua fixture sementara dibungkus transaksi dan di-rollback; token sementara dicabut.
- Suite backend: 125 test, 708 assertions, 5 error fixture SOSApiTest yang sama seperti sebelumnya (tabel users tidak disiapkan pada SQLite), 4 deprecation. Tidak ditambahkan file unit test.
- Migration, PHP lint, middleware route, serta diff whitespace diperiksa. Aset plus Figma 558 byte dan 24×24, dipakai dalam slot 24dp.
- Tampilan/interaksi langsung di HP atau emulator belum diverifikasi. Termasuk keyboard, font besar, toggle hijau/merah, dan perpindahan layar dengan isian belum disimpan.
