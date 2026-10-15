# Informasi demo per pos — backend lokal

Kontrak tambahan `GET /api/tracking/posts/{gate_id}` (Bearer Sanctum). Tidak ada route baru. Field lama tetap tersedia; setiap pos mendapat field tambahan berikut.

| Field | Tipe JSON | Nilai bila belum diisi |
|---|---|---|
| `mountain_status` | string/null | null |
| `water_source_status` | string | `Belum diketahui` |
| `camping_status` | string | `Belum diketahui` |
| `temperature_c` | number/null | null |
| `distance_to_next_m` | integer/null | null |
| `estimated_time_minutes` | integer/null | null |
| `distance_to_summit_m` | integer/null | null |
| `info_is_dummy` | boolean | false |
| `info_note` | string/null | null |
| `info_updated_at` | string ISO-8601/null | null |

`detail` pos juga disertakan sebagai string/null. Data sumber air memakai `Tersedia`, `Tidak tersedia`, `Belum diketahui`. Data berkemah memakai `Boleh`, `Tidak boleh`, `Belum diketahui`. Label ini merupakan nilai tersimpan, bukan hasil aturan berdasarkan urutan pos.

Seeder mengisi contoh statis untuk 11 pos Kersik Tuo dan 4 pos Solok Selatan. Semua contoh memiliki `info_is_dummy=true`, catatan **DATA DEMO**, serta waktu pengisian. Status Normal, sumber air, izin berkemah, suhu, jarak dan waktu pada contoh bukan informasi aktual lapangan. Elevasi dan koordinat dasar mengikuti seeder sebelumnya, dengan batas perkiraan yang telah dicatat. Tidak ada panggilan layanan cuaca, perhitungan jarak, atau estimasi waktu otomatis untuk field baru.

Angka jarak menggunakan meter, waktu menit dan suhu Celsius. Pada titik terakhir tiap jalur, jarak berikutnya, waktu berikutnya dan jarak ke puncak diisi 0. Field ini tidak mengganti status gunung aktual pada destinasi atau cuaca aktual `/api/beranda`. Konsumen wajib mempertahankan penanda demo ketika menampilkannya.

Seeder hanya mengisi info baru bila seluruh field informasinya masih kosong/default, atau baris sudah ditandai sebagai dummy. Info non-dummy yang sudah diisi dipertahankan. Pengisian ulang memperbarui waktu hanya bila nilai contoh berubah. Tidak ada data pengguna, booking, atau log check-in yang dihapus.

Perubahan ini hanya untuk backend lokal dan koleksi **TNKS API (Local)**. Mobile, source/deployment production dan koleksi **TNKS API (Production)** tidak diperbarui.
