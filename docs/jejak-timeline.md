# Timeline Jejak: hari tiket dan estimasi tiba

`GET /api/tracking/progress/{booking_id}` tetap memakai autentikasi dan
pemeriksaan anggota/pemilik booking yang sudah ada. Setiap elemen
`data.hikers[].checkpoints[]` memiliki tiga field tambahan:

| Field | Arti |
|---|---|
| `estimated_arrival_at` | Estimasi tiba ISO-8601 UTC, nullable. |
| `travel_minutes_from_previous` | Durasi dari pos sebelumnya ke pos ini, nullable. |
| `estimate_is_dummy` | Estimasi/durasi menggunakan data contoh, termasuk segmen sebelumnya yang memengaruhi estimasi. |

`estimated_time_minutes` di master pos adalah durasi dari **pos tersebut menuju
pos berikutnya**. Service menghitung estimasi per pendaki dari check-in aktual
yang tersedia dan menjumlahkan durasi segmen berikutnya. Check-in aktual di pos
lain menjadi acuan baru untuk estimasi selanjutnya. Durasi yang kosong memutus
rantai estimasi hingga ada check-in aktual berikutnya. Tidak ada jam
keberangkatan, jadwal istirahat, atau pembagian pos per hari yang dikarang.

Contoh: check-in gerbang 09.00 WIB dan durasi gerbang ke Pos 1 adalah 45 menit;
estimasi tiba Pos 1 adalah 09.45 WIB. Jika check-in Pos 1 terjadi pada 10.00 WIB,
estimasi pos berikutnya dihitung dari 10.00 WIB. Jam perkiraan menuju pos
yang telah selesai tetap terpisah dari waktu check-in aktualnya.

Android menampilkan estimasi di kiri kartu (`HH.mm`) dan check-in aktual di dalam
kartu (`Checkpoint pada HH.mm WIB`). Estimasi kosong tampil `—`. Tab hari dibentuk
dari tanggal masuk hingga tanggal keluar secara inklusif, sesuai perhitungan
`total_hari` saat pembelian tiket. Riwayat mengikuti tanggal check-in; pos mendatang
mengikuti tanggal estimasi, dalam zona Asia/Jakarta. Pos yang tanggalnya belum
diketahui ditampilkan pada hari ini jika masih dalam periode tiket, atau Hari 1
jika di luar periode. Hari lain yang belum memiliki jadwal menampilkan keadaan kosong.

Status kartu mengikuti pengguna yang login: selesai (hijau), pos pertama yang
belum selesai (biru), kemudian pos mendatang (abu-abu). Avatar anggota menunjukkan
kehadiran anggota tanpa menyelesaikan checkpoint pengguna lain. Setelah semua
selesai tidak ada kartu biru.

Android menyimpan durasi antarpos dan penanda data contoh pada cache master pos.
Migrasi Room 7 ke 8 menambah kolom tanpa membuang antrean check-in. Tanpa respons
estimasi yang masih tersedia di memori, aplikasi tidak menghitung jam tiba lokal
saat offline; waktu yang tidak tersedia tetap `—`. Check-in lokal tetap terlihat
dengan keterangan menunggu kirim.

## Verifikasi

- Build APK debug dan seluruh 39 tes JVM Android berhasil.
- Tes service estimasi, API kehadiran, dan data master pos: 21 tes, 160 assertions berhasil.
- Rangkaian backend penuh: tujuh tes SOS/GPS memerlukan tabel `users` yang tidak
  disiapkan oleh tes tersebut pada database SQLite in-memory. Tes terkait Jejak lolos.
- Tata letak belum diverifikasi secara visual pada perangkat/emulator.
- Tidak ada perubahan skema database backend atau deployment server dalam pekerjaan ini.
