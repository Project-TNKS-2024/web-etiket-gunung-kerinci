# Kontrak implementasi checkpoint naik–turun TNKS (v2)

Dokumen kontrak implementasi. Dibuat 16 Oktober 2026 dari spesifikasi
`Rencana implementasi checkpoint naik–turun TNKS` setelah audit workspace.
Dokumen ini mendefinisikan kontrak API v2, pemetaan segmen, aturan fase, dan
inventaris file/risiko aktual. Implementasi mengikuti tahap A–G.

## 1. Keputusan URI dan grup route

Route v2 hidup di bawah prefix `api/tracking/v2` (konsisten dengan prefix
`api/tracking` yang sudah ada). Semua endpoint dilindungi `auth:sanctum`.

| Metode | URI | Aksi |
|---|---|---|
| GET | `/api/tracking/v2/posts/{gate_id}` | Konfigurasi pos + `is_summit` + durasi turun |
| POST | `/api/tracking/v2/checkpoint/qr` | Check-in QR (naik/turun) |
| POST | `/api/tracking/v2/checkpoint/manual` | Check-in manual (naik/turun) |
| GET | `/api/tracking/v2/progress/{booking_id}` | Itinerary + progres per pendaki |
| DELETE | `/api/tracking/v2/checkpoint/{checkpoint_log_id}` | Pembatalan dengan aturan baru |

GPS detection (`POST /api/tracking/checkpoint/gps`) dipakai ulang: responsnya
tentang pos fisik, tidak bergantung fase.

## 2. Model data & identitas kunjungan

- `visit_key = phase + ':' + post_id`, contoh `naik:8` / `turun:8`.
- `visit_order` = urutan itinerary penuh (1..2N-1), bukan `urutan` pos fisik.
- Puncak hanya memakai `visit_key` fase `naik`; tidak ada `turun:<puncak>`.
- Identitas lintas pendaki (log/DB): `booking_id + id_pendaki + id_post + phase`.
- `client_event_id` (UUID) dibuat sekali di mobile saat menyimpan event, dipakai
  ulang pada semua retry; unik untuk nilai terisi.

### Timeline

Pos naik `P1..PN` (PN puncak). Itinerary = `naik:P1..naik:PN` lalu
`turun:P(N-1)..turun:P1`. Jumlah kunjungan `2N-1`.

### Pemetaan segmen (arah ternormalisasi)

- Naik `Pi → P(i+1)`: jarak `Pi.distance_to_next_m`, durasi `Pi.estimated_time_minutes`.
- Turun `Pi → P(i-1)`: jarak `P(i-1).distance_to_next_m`, durasi `Pi.estimated_descent_time_minutes`.
- Puncak → pos sebelumnya: jarak milik `P(N-1).distance_to_next_m`, durasi `PN.estimated_descent_time_minutes`.
- `estimated_descent_time_minutes` null = belum tersedia; angka nol valid (≠ null).

## 3. Kode error mesin (dikembalikan di `errors`)

| Kode | HTTP | Arti |
|---|---|---|
| `CHECKPOINT_ALREADY_RECORDED` | 409 | Tuple kunjungan sudah ada |
| `DESCENT_REQUIRES_SUMMIT` | 409 | Summit naik target belum tercatat |
| `CHECKPOINT_PHASE_CONFLICT` | 409 | Fase/waktu kejadian tidak cocok |
| `SUMMIT_HAS_DESCENT_CHECKINS` | 409 | Summit tidak boleh dibatalkan |
| `CLIENT_EVENT_CONFLICT` | 409 | UUID dipakai payload berbeda |
| `ROUTE_CONFIGURATION_INVALID` | 422 | Jalur/puncak tidak valid |

Replay `client_event_id` identik → 200 + record canonical + `idempotent=true`.

## 4. Inventaris file aktual (hasil audit 16 Okt 2026)

### Backend (`web-etiket-gunung-kerinci`)

Konsumen checkpoint saat ini HANYA `CheckpointController` + `CheckpointTimelineService`
+ model `GkCheckpointLog`/`GkPost` + test. Admin monitoring (`MonitoringController`)
memakai `gk_tracking`, bukan `gk_checkpoint_logs`, jadi tidak terdampak persentase.

- `app/Http/Controllers/API/CheckpointController.php` — QR/manual/cancel/posts/progress (v1).
- `app/Services/CheckpointTimelineService.php` — timeline naik satu arah.
- `app/Models/GkCheckpointLog.php` — belum ada `phase`/`client_event_id`.
- `app/Models/GkPost.php`, `app/Models/gk_pendaki.php`.
- `database/migrations/72_gk_checkpoint_logs.php` — unique `unique_checkin` (3 kolom).
- `database/migrations/70_gk_posts.php`, `77_add_demo_info_to_gk_posts.php`.
- `database/seeders/TrailPostSeeder.php` — demo; Puncak Kerinci = urutan terakhir.
- `routes/api/routeTracking.php` — route v1.
- Tests: `CheckpointAttendanceApiTest.php`, `TrailPostInfoApiTest.php`,
  `EmergencyTrackingApiTest.php`, `Unit/CheckpointTimelineServiceTest.php`.

Branch: `satria-bahari` (ahead 9 commit). Ada file tak terlacak (_chk.php, _db.php,
_e2e.sh, _newman_*, _tnks.json) — TIDAK disentuh.

### Mobile (`mobile-gunung-kerinci`)

- `model/` TrackingPostResponse, TrackingProgressResponse, CheckpointQrRequest/Response,
  ManualCheckInRequest/Response, CancelCheckpointResponse.
- `network/ApiService.kt` — endpoint v1.
- `data/local/TnksDatabase.kt` versi 11 (jarak), `checkpoint/CheckpointLogEntity`+DAO,
  `TrackingPostEntity`+DAO.
- `data/repository/TrackingRepository.kt`, CheckpointQrRepository, ManualCheckInRepository,
  TrackingProgressRepository.
- `data/sync/CheckpointSyncWorker.kt` (409 = sukses — WAJIB diperbaiki), SyncScheduler,
  SyncStatus, SyncDefaults.
- `ui/pelacakan_jejak/` PelacakanJejakViewModel (himpunan `completedPostIds`),
  JejakUiModels, JejakTimelineMapper, TrackingPostUiMapper, TrackingProgressUiMapper,
  LocationDetailUiMapper, PelacakanJejakScreen, PelacakanJejakLocationDetailScreen.

Branch: `satria-bahari` (ahead 4 commit). Perubahan lokal belum commit (jarak/estimasi
dari pekerjaan sebelumnya) + `LocationDetailDistanceTest.kt` tak terlacak — DIJAGA,
bukan dianggap hasil pekerjaan baru. Migrasi Room berikutnya = 12.

## 5. Risiko dan keputusan desain

- Rollback setelah ada log turun tidak sederhana (index 3 kolom lama bisa gagal) —
  didokumentasikan, tidak menghapus log turun otomatis.
- Konfigurasi jalur harus tepat satu puncak aktif di urutan terakhir; bila tidak,
  itinerary mengembalikan `ROUTE_CONFIGURATION_INVALID`. Penamaan hanya untuk backfill.
- Legacy v1 tetap proyeksi naik; request tanpa `phase` diperlakukan naik.
