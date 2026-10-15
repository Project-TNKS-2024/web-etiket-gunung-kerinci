# TNKS Postman — audit 15 Oktober 2026

Koleksi ini mendokumentasikan seluruh route Laravel dalam inventaris audit:

| Target | Route | Request inventaris | Alur lama | Total request |
|---|---:|---:|---:|---:|
| Production | 189 | 299 | 20 | 319 |
| Local | 188 | 297 | 20 | 317 |

GET/HEAD dan seluruh method fallback dipisahkan. Folder Route Inventory memuat action, middleware, validasi literal dari source dan template body. Validasi dinamis serta prasyarat bisnis tetap perlu diperiksa; template bukan hasil pengujian. Contoh response alur lama belum diverifikasi.

`base_url` adalah origin tanpa `/api`. Variabel environment dapat menimpa variabel koleksi: jangan memilih environment lokal ketika menargetkan production. Token/password kosong. Simpan kredensial sebagai nilai privat/lokal, bukan di Git.

`allow_requests=false` adalah default. Guard per request melewatkan seluruh request selain GET health, fallback audit, dan penolakan tracking tanpa token. Untuk uji manual request lain, gunakan data uji yang sah dan ubah `allow_requests=true`. Jangan menjalankan seluruh koleksi production dalam kondisi tersebut. Web/admin membutuhkan cookie sesi, izin dan CSRF; Bearer Sanctum dipakai pada API yang memerlukannya.

Sumber production telah dibandingkan dengan lokal melalui manifest hash. Production memperbaiki resolver pendaki anggota pada tracking/emergency/SOS serta pembatasan gate checkpoint QR/manual. Route tambahan production adalah `biscolab-recaptcha/validate`.

Verifikasi: struktur koleksi tanpa error/warning; production `/up` 200, fallback audit 404, tracking tanpa token 401; guard SOS diuji dengan origin lokal pengganti dan tidak mengirim request. Audit ini tidak menjalankan SOS, transaksi, atau uji bisnis dengan akun production. Source aplikasi tidak diubah.

Workspace: `c10d748d-f653-4744-81e6-b134997e8b50`. Koleksi production `55136218-79824cce-3b46-4ba9-a5be-2b0c16edf9f7`; lokal `55136218-9ba9eb50-35a2-4f5b-8356-1ddf3a6846ec`. Saat sinkronisasi gunakan strategi `default` untuk create/update tanpa menghapus resource lain.

Environment dan globals hasil pull disimpan lokal dan diabaikan oleh Git untuk mencegah kredensial ikut ter-commit. Laporan lengkap, cadangan sebelum/sesudah, tabel route dan bukti hash berada di folder audit skripsi `audit-context/production-comparison-2026-10-15/`.
