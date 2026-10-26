# Informasi per pos — backend lokal

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
| `info_note` | string/null | null |
| `info_updated_at` | string ISO-8601/null | null |

`detail` pos juga disertakan sebagai string/null. Data sumber air memakai `Tersedia`, `Tidak tersedia`, `Belum diketahui`. Data berkemah memakai `Boleh`, `Tidak boleh`, `Belum diketahui`. Label ini merupakan nilai tersimpan, bukan hasil aturan berdasarkan urutan pos.

Seeder mengikuti konfigurasi jalur pada TrailPostSeeder. Nilai yang belum tersedia tetap null atau Belum diketahui. Data konfigurasi bukan hasil layanan cuaca otomatis.

Angka jarak menggunakan meter, waktu menit dan suhu Celsius. Pada titik terakhir tiap jalur, jarak berikutnya, waktu berikutnya dan jarak ke puncak diisi 0. Field ini tidak mengganti status gunung aktual pada destinasi atau cuaca aktual `/api/beranda`.

Penanda data dummy telah dihapus dari kontrak API. Migrasi 90 menghapus kolomnya dari gk_posts; migrasi historis tetap dipertahankan. Tidak ada penghapusan data booking atau log check-in.

Perubahan ini hanya untuk backend lokal dan koleksi **TNKS API (Local)**. Mobile, source/deployment production dan koleksi **TNKS API (Production)** tidak diperbarui.
