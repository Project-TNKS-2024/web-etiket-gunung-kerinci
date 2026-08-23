# Dokumentasi API: Emergency, Tracking & SOS

> **Versi**: 1.0  
> **Tanggal**: 2026-05-12  
> **Base URL**: `/api`  
> **Autentikasi**: Bearer Token (Laravel Sanctum)

---

## Informasi Umum

### Autentikasi

Semua endpoint memerlukan token Sanctum di header:
```
Authorization: Bearer {token}
```

Token didapat dari endpoint login (`POST /api/login`).

### Format Response Standar

Semua response menggunakan format:
```json
{
    "success": true/false,
    "message": "Pesan deskriptif",
    "data": { ... } atau null,
    "errors": { ... } atau null
}
```

### Kode Status HTTP

| Kode | Arti |
|------|------|
| 200 | Berhasil |
| 201 | Berhasil dibuat |
| 403 | Tidak ada pendakian aktif / akses ditolak |
| 404 | Resource tidak ditemukan |
| 409 | Konflik (duplikat) |
| 422 | Validasi gagal |
| 429 | Terlalu banyak request (cooldown) |

### Syarat Pendakian Aktif

Sebagian besar endpoint memerlukan user memiliki pendakian aktif:
- `gk_bookings.status_booking = 6` (sudah check-in)
- `gk_bookings.id_user = user yang login`

Jika tidak ada pendakian aktif, response:
```json
{
    "success": false,
    "message": "Tidak ada pendakian aktif",
    "data": null,
    "errors": null
}
```

---

## 1. GPS Tracking

### 1.1 Upload GPS (Single)

Upload posisi GPS terkini dari perangkat mobile.

**Endpoint:** `POST /api/tracking/gps`  
**Auth:** Bearer Token  
**Syarat:** Pendakian aktif

#### Cara Penggunaan:
1. Dapatkan koordinat GPS dari perangkat
2. Kirim request dengan latitude dan longitude
3. Tunggu response 201 sebagai konfirmasi
4. Ulangi setiap 60 detik (rate limit: 1x per 30 detik)

#### Parameter Input:

| Parameter | Tipe | Wajib | Validasi | Keterangan |
|-----------|------|-------|----------|------------|
| latitude | numeric | ✅ | -90 s/d 90 | Koordinat lintang |
| longitude | numeric | ✅ | -180 s/d 180 | Koordinat bujur |
| altitude | numeric | ❌ | - | Ketinggian (meter) |
| accuracy | numeric | ❌ | min: 0 | Akurasi GPS (meter) |
| battery_level | integer | ❌ | 0–100 | Level baterai perangkat |
| recorded_at | datetime | ❌ | format ISO 8601 | Waktu perekaman (default: sekarang) |

#### Contoh Request:
```json
POST /api/tracking/gps
Authorization: Bearer {token}
Content-Type: application/json

{
    "latitude": -1.6974,
    "longitude": 101.2642,
    "altitude": 2800.5,
    "accuracy": 12.3,
    "battery_level": 75,
    "recorded_at": "2026-05-12T08:30:00+07:00"
}
```

#### Response Berhasil (201):
```json
{
    "success": true,
    "message": "GPS berhasil direkam",
    "data": {
        "id": 42,
        "server_time": "2026-05-12T01:30:05.123Z"
    },
    "errors": null
}
```

#### Response Gagal — Rate Limit (429):
```json
{
    "success": false,
    "message": "Terlalu cepat, tunggu 30 detik",
    "data": null,
    "errors": null
}
```

---

### 1.2 Upload GPS Batch (Offline Sync)

Upload banyak posisi GPS sekaligus setelah perangkat kembali online.

**Endpoint:** `POST /api/tracking/gps/batch`  
**Auth:** Bearer Token  
**Syarat:** Pendakian aktif

#### Cara Penggunaan:
1. Simpan posisi GPS secara lokal saat offline
2. Saat koneksi kembali, kirim semua posisi dalam satu request
3. Maksimal 100 posisi per request
4. Setiap posisi harus memiliki `recorded_at`

#### Parameter Input:

| Parameter | Tipe | Wajib | Validasi | Keterangan |
|-----------|------|-------|----------|------------|
| positions | array | ✅ | min: 1, max: 100 | Array posisi GPS |
| positions.*.latitude | numeric | ✅ | -90 s/d 90 | Koordinat lintang |
| positions.*.longitude | numeric | ✅ | -180 s/d 180 | Koordinat bujur |
| positions.*.altitude | numeric | ❌ | - | Ketinggian (meter) |
| positions.*.accuracy | numeric | ❌ | min: 0 | Akurasi GPS (meter) |
| positions.*.battery_level | integer | ❌ | 0–100 | Level baterai |
| positions.*.recorded_at | datetime | ✅ | format ISO 8601 | Waktu perekaman |

#### Contoh Request:
```json
POST /api/tracking/gps/batch
Authorization: Bearer {token}
Content-Type: application/json

{
    "positions": [
        {
            "latitude": -1.6974,
            "longitude": 101.2642,
            "altitude": 2800,
            "battery_level": 80,
            "recorded_at": "2026-05-12T08:00:00+07:00"
        },
        {
            "latitude": -1.6980,
            "longitude": 101.2650,
            "altitude": 2850,
            "battery_level": 78,
            "recorded_at": "2026-05-12T08:01:00+07:00"
        }
    ]
}
```

#### Response Berhasil (201):
```json
{
    "success": true,
    "message": "Batch GPS berhasil direkam (2 posisi)",
    "data": {
        "inserted": 2,
        "server_time": "2026-05-12T01:35:00.000Z"
    },
    "errors": null
}
```

---

### 1.3 Posisi Terakhir Saya

Ambil posisi GPS terakhir yang terekam.

**Endpoint:** `GET /api/tracking/my-position`  
**Auth:** Bearer Token  
**Syarat:** Pendakian aktif

#### Cara Penggunaan:
1. Panggil endpoint ini untuk mengetahui posisi terakhir yang tercatat di server
2. Berguna untuk sinkronisasi setelah offline

#### Parameter Input: Tidak ada

#### Contoh Request:
```
GET /api/tracking/my-position
Authorization: Bearer {token}
```

#### Response Berhasil (200):
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "latitude": "-1.69740000",
        "longitude": "101.26420000",
        "altitude": "2800.50",
        "accuracy": "12.30",
        "battery_level": 75,
        "recorded_at": "2026-05-12T08:30:00.000Z"
    },
    "errors": null
}
```

#### Response — Belum Ada Posisi (200):
```json
{
    "success": true,
    "message": "Belum ada posisi terekam",
    "data": null,
    "errors": null
}
```


---

## 2. Checkpoint / Pelacakan Jejak

### 2.1 Daftar Pos Jalur

Ambil semua pos (checkpoint) pada jalur tertentu.

**Endpoint:** `GET /api/tracking/posts/{gate_id}`  
**Auth:** Bearer Token

#### Cara Penggunaan:
1. Dapatkan `gate_id` dari data booking (gate masuk pendaki)
2. Panggil endpoint untuk mendapatkan daftar pos beserta koordinat
3. Gunakan data ini untuk menampilkan peta jalur di mobile

#### Parameter URL:

| Parameter | Tipe | Keterangan |
|-----------|------|------------|
| gate_id | integer | ID gate/jalur pendakian |

#### Contoh Request:
```
GET /api/tracking/posts/1
Authorization: Bearer {token}
```

#### Response Berhasil (200):
```json
{
    "success": true,
    "message": "Success",
    "data": [
        {
            "id": 1,
            "nama": "Shelter 1",
            "urutan": 1,
            "latitude": "-1.70800000",
            "longitude": "101.26800000",
            "altitude": 2100,
            "radius_meter": 150
        },
        {
            "id": 2,
            "nama": "Pos 1",
            "urutan": 2,
            "latitude": "-1.70400000",
            "longitude": "101.26600000",
            "altitude": 2400,
            "radius_meter": 150
        },
        {
            "id": 5,
            "nama": "Puncak Kerinci",
            "urutan": 5,
            "latitude": "-1.69740000",
            "longitude": "101.26420000",
            "altitude": 3805,
            "radius_meter": 200
        }
    ],
    "errors": null
}
```

---

### 2.2 Check-in via QR Code

Pendaki scan QR code di pos untuk mencatat kehadiran.

**Endpoint:** `POST /api/tracking/checkpoint/qr`  
**Auth:** Bearer Token  
**Syarat:** Pendakian aktif

#### Cara Penggunaan:
1. Pendaki scan QR code yang terpasang di pos jalur
2. Kirim nilai QR code ke endpoint ini
3. Sertakan koordinat GPS saat ini (opsional tapi disarankan)
4. Sistem mencatat check-in dan mengembalikan progress

#### Parameter Input:

| Parameter | Tipe | Wajib | Validasi | Keterangan |
|-----------|------|-------|----------|------------|
| qr_code_value | string | ✅ | harus ada di gk_posts | Nilai dari QR code yang di-scan |
| latitude | numeric | ❌ | -90 s/d 90 | Koordinat saat scan |
| longitude | numeric | ❌ | -180 s/d 180 | Koordinat saat scan |

#### Contoh Request:
```json
POST /api/tracking/checkpoint/qr
Authorization: Bearer {token}
Content-Type: application/json

{
    "qr_code_value": "POST-A1B2C3D4E5F6",
    "latitude": -1.7040,
    "longitude": 101.2660
}
```

#### Response Berhasil (201):
```json
{
    "success": true,
    "message": "Check-in berhasil di Pos 1",
    "data": {
        "checkpoint_log_id": 15,
        "post": {
            "id": 2,
            "nama": "Pos 1",
            "urutan": 2
        },
        "progress": {
            "completed": 2,
            "total": 5,
            "percentage": 40
        }
    },
    "errors": null
}
```

#### Response Gagal — QR Tidak Valid (404):
```json
{
    "success": false,
    "message": "QR code tidak valid",
    "data": null,
    "errors": null
}
```

#### Response Gagal — Sudah Check-in (409):
```json
{
    "success": false,
    "message": "Sudah check-in di pos ini",
    "data": null,
    "errors": {
        "checked_at": "2026-05-12T08:00:00.000Z"
    }
}
```

---

### 2.3 Deteksi Kedekatan GPS

Cek apakah pendaki berada dalam radius pos tertentu.

**Endpoint:** `POST /api/tracking/checkpoint/gps`  
**Auth:** Bearer Token  
**Syarat:** Pendakian aktif

#### Cara Penggunaan:
1. Kirim koordinat GPS pendaki saat ini
2. Sistem menghitung jarak ke semua pos di jalur
3. Jika `within_radius: true`, tampilkan tombol "Check-in" di mobile
4. Endpoint ini TIDAK otomatis mencatat check-in (perlu konfirmasi manual)

#### Parameter Input:

| Parameter | Tipe | Wajib | Validasi | Keterangan |
|-----------|------|-------|----------|------------|
| latitude | numeric | ✅ | -90 s/d 90 | Koordinat pendaki |
| longitude | numeric | ✅ | -180 s/d 180 | Koordinat pendaki |
| accuracy | numeric | ❌ | min: 0 | Akurasi GPS (meter) |

#### Contoh Request:
```json
POST /api/tracking/checkpoint/gps
Authorization: Bearer {token}
Content-Type: application/json

{
    "latitude": -1.7042,
    "longitude": 101.2658,
    "accuracy": 10.5
}
```

#### Response Berhasil (200):
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "nearest_post": {
            "id": 2,
            "nama": "Pos 1",
            "distance_meters": 85.42,
            "within_radius": true,
            "radius_meter": 150
        },
        "all_posts": [
            {
                "id": 1,
                "nama": "Shelter 1",
                "urutan": 1,
                "distance_meters": 520.30,
                "within_radius": false
            },
            {
                "id": 2,
                "nama": "Pos 1",
                "urutan": 2,
                "distance_meters": 85.42,
                "within_radius": true
            },
            {
                "id": 3,
                "nama": "Pos 2",
                "urutan": 3,
                "distance_meters": 1250.80,
                "within_radius": false
            }
        ]
    },
    "errors": null
}
```

---

### 2.4 Check-in Manual

Pendaki menekan tombol check-in secara manual di pos tertentu.

**Endpoint:** `POST /api/tracking/checkpoint/manual`  
**Auth:** Bearer Token  
**Syarat:** Pendakian aktif

#### Cara Penggunaan:
1. Pendaki memilih pos dari daftar
2. Tekan tombol "Check-in Manual"
3. Sistem mencatat dengan flag `is_manual_override` jika jarak > 500m
4. Admin dapat mereview check-in manual yang jauh dari pos

#### Parameter Input:

| Parameter | Tipe | Wajib | Validasi | Keterangan |
|-----------|------|-------|----------|------------|
| post_id | integer | ✅ | harus ada di gk_posts | ID pos tujuan |
| latitude | numeric | ✅ | -90 s/d 90 | Koordinat pendaki saat ini |
| longitude | numeric | ✅ | -180 s/d 180 | Koordinat pendaki saat ini |

#### Contoh Request:
```json
POST /api/tracking/checkpoint/manual
Authorization: Bearer {token}
Content-Type: application/json

{
    "post_id": 3,
    "latitude": -1.7000,
    "longitude": 101.2645
}
```

#### Response Berhasil (201):
```json
{
    "success": true,
    "message": "Check-in manual berhasil di Pos 2",
    "data": {
        "checkpoint_log_id": 16,
        "post": {
            "id": 3,
            "nama": "Pos 2",
            "urutan": 3
        },
        "is_manual_override": false,
        "distance_from_post": 125.30,
        "progress": {
            "completed": 3,
            "total": 5,
            "percentage": 60
        }
    },
    "errors": null
}
```

> **Catatan:** `is_manual_override: true` jika pendaki > 500m dari pos.

---

### 2.5 Progress Pendakian

Lihat progress checkpoint untuk satu booking (semua pendaki dalam grup).

**Endpoint:** `GET /api/tracking/progress/{booking_id}`  
**Auth:** Bearer Token

#### Cara Penggunaan:
1. Gunakan `booking_id` dari data booking aktif
2. Response menampilkan progress setiap pendaki dalam grup
3. Setiap checkpoint menunjukkan apakah sudah dilalui, kapan, dan metode

#### Parameter URL:

| Parameter | Tipe | Keterangan |
|-----------|------|------------|
| booking_id | string (UUID) | ID booking |

#### Contoh Request:
```
GET /api/tracking/progress/744b875c-c7c2-457e-a12d-e5feafb839d0
Authorization: Bearer {token}
```

#### Response Berhasil (200):
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "booking_id": "744b875c-c7c2-457e-a12d-e5feafb839d0",
        "gate": "Desa Kersik Tuo",
        "total_posts": 5,
        "hikers": [
            {
                "pendaki_id": "uuid-pendaki-1",
                "nama": "John Doe",
                "completed": 3,
                "total": 5,
                "percentage": 60,
                "checkpoints": [
                    {
                        "post_id": 1,
                        "nama": "Shelter 1",
                        "urutan": 1,
                        "completed": true,
                        "checked_at": "2026-05-12T06:00:00.000Z",
                        "method": "qr"
                    },
                    {
                        "post_id": 2,
                        "nama": "Pos 1",
                        "urutan": 2,
                        "completed": true,
                        "checked_at": "2026-05-12T08:30:00.000Z",
                        "method": "gps"
                    },
                    {
                        "post_id": 3,
                        "nama": "Pos 2",
                        "urutan": 3,
                        "completed": true,
                        "checked_at": "2026-05-12T11:00:00.000Z",
                        "method": "manual"
                    },
                    {
                        "post_id": 4,
                        "nama": "Pos 3 (Shelter 2)",
                        "urutan": 4,
                        "completed": false,
                        "checked_at": null,
                        "method": null
                    },
                    {
                        "post_id": 5,
                        "nama": "Puncak Kerinci",
                        "urutan": 5,
                        "completed": false,
                        "checked_at": null,
                        "method": null
                    }
                ]
            }
        ]
    },
    "errors": null
}
```


---

## 3. Pesan Darurat (Emergency)

### 3.1 Kirim Pesan Darurat

Pendaki mengirim pesan darurat ke admin.

**Endpoint:** `POST /api/emergency/trigger`  
**Auth:** Bearer Token  
**Syarat:** Pendakian aktif

#### Cara Penggunaan:
1. Pendaki mengisi judul dan deskripsi keadaan darurat
2. Pilih tingkat keparahan (severity)
3. Sertakan koordinat GPS (jika tidak disertakan, sistem menggunakan posisi GPS terakhir)
4. Admin menerima notifikasi real-time via WebSocket

#### Parameter Input:

| Parameter | Tipe | Wajib | Validasi | Keterangan |
|-----------|------|-------|----------|------------|
| title | string | ✅ | max: 255 | Judul pesan darurat |
| description | string | ✅ | max: 2000 | Deskripsi detail keadaan |
| severity | string | ✅ | low/medium/high/critical | Tingkat keparahan |
| latitude | numeric | ❌ | -90 s/d 90 | Koordinat (fallback ke GPS terakhir) |
| longitude | numeric | ❌ | -180 s/d 180 | Koordinat (fallback ke GPS terakhir) |

#### Tingkat Severity:
- `low` — Butuh informasi/bantuan ringan
- `medium` — Cedera ringan/tersesat
- `high` — Cedera serius/kondisi berbahaya
- `critical` — Mengancam jiwa

#### Contoh Request:
```json
POST /api/emergency/trigger
Authorization: Bearer {token}
Content-Type: application/json

{
    "title": "Tersesat di jalur",
    "description": "Saya tersesat setelah Pos 2, kabut tebal dan tidak bisa menemukan jalur",
    "severity": "high",
    "latitude": -1.6980,
    "longitude": 101.2650
}
```

#### Response Berhasil (201):
```json
{
    "success": true,
    "message": "Pesan darurat berhasil dikirim",
    "data": {
        "id": 7,
        "status": "active",
        "created_at": "2026-05-12T08:35:00.000Z"
    },
    "errors": null
}
```

---

### 3.2 Lihat Pesan Darurat Aktif

Ambil semua pesan darurat aktif di destinasi pendaki.

**Endpoint:** `GET /api/emergency/active`  
**Auth:** Bearer Token  
**Syarat:** Pendakian aktif

#### Cara Penggunaan:
1. Panggil endpoint ini secara berkala atau saat menerima notifikasi WebSocket
2. Menampilkan pesan darurat dari admin (broadcast) dan pendaki lain
3. Gunakan untuk menampilkan peringatan di mobile app

#### Parameter Input: Tidak ada

#### Contoh Request:
```
GET /api/emergency/active
Authorization: Bearer {token}
```

#### Response Berhasil (200):
```json
{
    "success": true,
    "message": "Success",
    "data": [
        {
            "id": 8,
            "type": "admin_broadcast",
            "title": "Cuaca Buruk - Turun Segera",
            "description": "Badai diperkirakan tiba dalam 2 jam. Semua pendaki diminta turun.",
            "severity": "critical",
            "latitude": null,
            "longitude": null,
            "created_at": "2026-05-12T06:00:00.000Z"
        },
        {
            "id": 7,
            "type": "hiker_alert",
            "title": "Pendaki cedera di Pos 3",
            "description": "Ada pendaki cedera kaki di dekat Pos 3",
            "severity": "medium",
            "latitude": "-1.69700000",
            "longitude": "101.26350000",
            "created_at": "2026-05-12T05:30:00.000Z"
        }
    ],
    "errors": null
}
```


---

## 4. SOS (Tombol Panik)

### 4.1 Kirim SOS

Pendaki menekan tombol panik untuk meminta bantuan darurat.

**Endpoint:** `POST /api/sos/trigger`  
**Auth:** Bearer Token  
**Syarat:** Pendakian aktif  
**Cooldown:** Maksimal 1 SOS per 5 menit

#### Cara Penggunaan:
1. Pendaki menekan tombol SOS (disarankan: tahan 3 detik atau double-tap)
2. Koordinat GPS wajib disertakan
3. Pilih tingkat keparahan
4. Sistem mengirim notifikasi real-time ke admin
5. Response berisi kontak tim SAR untuk dihubungi langsung

#### Parameter Input:

| Parameter | Tipe | Wajib | Validasi | Keterangan |
|-----------|------|-------|----------|------------|
| latitude | numeric | ✅ | -90 s/d 90 | Koordinat pendaki |
| longitude | numeric | ✅ | -180 s/d 180 | Koordinat pendaki |
| severity | string | ✅ | low/medium/high | Tingkat keparahan |
| message | string | ❌ | max: 1000 | Pesan singkat (opsional) |

#### Tingkat Severity SOS:
- `low` — Butuh bantuan (kelelahan, kehabisan air)
- `medium` — Cedera/tersesat
- `high` — Mengancam jiwa (jatuh, tidak bisa bergerak)

#### Contoh Request:
```json
POST /api/sos/trigger
Authorization: Bearer {token}
Content-Type: application/json

{
    "latitude": -1.6974,
    "longitude": 101.2642,
    "severity": "high",
    "message": "Saya terjatuh dan tidak bisa bergerak, kaki patah"
}
```

#### Response Berhasil (201):
```json
{
    "success": true,
    "message": "SOS berhasil dikirim. Bantuan sedang dalam perjalanan.",
    "data": {
        "sos_id": 5,
        "status": "active",
        "created_at": "2026-05-12T09:00:00.000Z",
        "rescue_contact": {
            "phone": "+6281234567890",
            "whatsapp_link": "https://wa.me/6281234567890"
        }
    },
    "errors": null
}
```

#### Response Gagal — Cooldown (429):
```json
{
    "success": false,
    "message": "Tunggu 180 detik sebelum mengirim SOS lagi",
    "data": null,
    "errors": {
        "wait_seconds": 180
    }
}
```

---

### 4.2 Lihat SOS Aktif Saya

Cek status SOS yang sedang aktif.

**Endpoint:** `GET /api/sos/active`  
**Auth:** Bearer Token  
**Syarat:** Pendakian aktif

#### Cara Penggunaan:
1. Panggil setelah mengirim SOS untuk memantau status
2. Status berubah saat admin merespons (acknowledged → dispatched → resolved)
3. Gunakan untuk menampilkan status bantuan di mobile

#### Parameter Input: Tidak ada

#### Contoh Request:
```
GET /api/sos/active
Authorization: Bearer {token}
```

#### Response Berhasil — Ada SOS Aktif (200):
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "id": 5,
        "severity": "high",
        "message": "Saya terjatuh dan tidak bisa bergerak",
        "status": "acknowledged",
        "latitude": "-1.69740000",
        "longitude": "101.26420000",
        "created_at": "2026-05-12T09:00:00.000Z"
    },
    "errors": null
}
```

#### Response — Tidak Ada SOS Aktif (200):
```json
{
    "success": true,
    "message": "Tidak ada SOS aktif",
    "data": null,
    "errors": null
}
```

#### Status SOS:
- `active` — SOS baru dikirim, menunggu respons admin
- `acknowledged` — Admin sudah menerima dan memproses
- `dispatched` — Tim SAR sudah dikirim ke lokasi
- `resolved` — Masalah selesai
- `false_alarm` — Alarm palsu (ditandai admin)


---

## 5. SOS Chat

### 5.1 Kirim Pesan Chat

Kirim pesan teks atau gambar dalam sesi SOS.

**Endpoint:** `POST /api/sos/chat/{sos_id}/send`  
**Auth:** Bearer Token  
**Syarat:** Harus pemilik SOS atau admin destinasi

#### Cara Penggunaan:
1. Setelah SOS aktif, buka halaman chat
2. Kirim pesan teks atau foto untuk berkomunikasi dengan admin/tim SAR
3. Pesan dikirim real-time via WebSocket ke pihak lain
4. Gambar maksimal 5MB (JPEG/PNG)

#### Parameter URL:

| Parameter | Tipe | Keterangan |
|-----------|------|------------|
| sos_id | integer | ID SOS yang aktif |

#### Parameter Input (Teks):

| Parameter | Tipe | Wajib | Validasi | Keterangan |
|-----------|------|-------|----------|------------|
| type | string | ✅ | text/image | Jenis pesan |
| content | string | ✅ (jika text) | max: 2000 | Isi pesan teks |

#### Parameter Input (Gambar):

| Parameter | Tipe | Wajib | Validasi | Keterangan |
|-----------|------|-------|----------|------------|
| type | string | ✅ | text/image | Jenis pesan |
| image | file | ✅ (jika image) | jpeg/png/jpg, max: 5MB | File gambar |

#### Contoh Request (Teks):
```json
POST /api/sos/chat/5/send
Authorization: Bearer {token}
Content-Type: application/json

{
    "type": "text",
    "content": "Saya di dekat pohon besar setelah Pos 2, ada batu besar di sebelah kiri"
}
```

#### Contoh Request (Gambar):
```
POST /api/sos/chat/5/send
Authorization: Bearer {token}
Content-Type: multipart/form-data

type=image
image=@foto_lokasi.jpg
```

#### Response Berhasil (201):
```json
{
    "success": true,
    "message": "Pesan terkirim",
    "data": {
        "id": 12,
        "sender_type": "hiker",
        "type": "text",
        "content": "Saya di dekat pohon besar setelah Pos 2",
        "created_at": "2026-05-12T09:05:00.000Z"
    },
    "errors": null
}
```

#### Response Gagal — SOS Tidak Ditemukan (404):
```json
{
    "success": false,
    "message": "SOS tidak ditemukan",
    "data": null,
    "errors": null
}
```

#### Response Gagal — Tidak Punya Akses (403):
```json
{
    "success": false,
    "message": "Tidak memiliki akses ke SOS ini",
    "data": null,
    "errors": null
}
```

---

### 5.2 Riwayat Pesan Chat

Ambil semua pesan dalam sesi SOS (paginasi).

**Endpoint:** `GET /api/sos/chat/{sos_id}/messages`  
**Auth:** Bearer Token  
**Syarat:** Harus pemilik SOS atau admin destinasi

#### Cara Penggunaan:
1. Panggil saat membuka halaman chat
2. Pesan diurutkan dari terlama ke terbaru
3. Pesan yang belum dibaca otomatis ditandai "read"
4. Gunakan pagination jika pesan banyak

#### Parameter URL:

| Parameter | Tipe | Keterangan |
|-----------|------|------------|
| sos_id | integer | ID SOS |

#### Parameter Query (Opsional):

| Parameter | Tipe | Default | Keterangan |
|-----------|------|---------|------------|
| page | integer | 1 | Halaman pagination |

#### Contoh Request:
```
GET /api/sos/chat/5/messages
Authorization: Bearer {token}
```

#### Response Berhasil (200):
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "messages": [
            {
                "id": 10,
                "sender_type": "hiker",
                "sender_name": "John",
                "type": "text",
                "content": "Tolong, saya terjatuh",
                "is_read": true,
                "created_at": "2026-05-12T09:00:00.000Z"
            },
            {
                "id": 11,
                "sender_type": "admin",
                "sender_name": "Admin",
                "type": "text",
                "content": "Tim SAR sedang menuju lokasi Anda. Tetap tenang.",
                "is_read": true,
                "created_at": "2026-05-12T09:02:00.000Z"
            },
            {
                "id": 12,
                "sender_type": "hiker",
                "sender_name": "John",
                "type": "image",
                "content": "/storage/sos/5/foto_lokasi.jpg",
                "is_read": false,
                "created_at": "2026-05-12T09:05:00.000Z"
            }
        ],
        "pagination": {
            "current_page": 1,
            "last_page": 1,
            "total": 3
        }
    },
    "errors": null
}
```

---

## 6. Kontak Darurat & Laporan Bencana

### 6.1 Kontak Darurat (WhatsApp & Telepon)

Ambil daftar kontak tim SAR dengan link WhatsApp.

**Endpoint:** `GET /api/sos/call-options`  
**Auth:** Bearer Token

#### Cara Penggunaan:
1. Panggil endpoint ini saat pendaki membuka halaman SOS
2. Tampilkan tombol "Hubungi via WhatsApp" dan "Telepon"
3. Link WhatsApp sudah berisi pesan pre-filled dengan nama dan booking ID

#### Parameter Input: Tidak ada

#### Contoh Request:
```
GET /api/sos/call-options
Authorization: Bearer {token}
```

#### Response Berhasil (200):
```json
{
    "success": true,
    "message": "Kontak darurat",
    "data": [
        {
            "name": "Tim SAR TNKS",
            "phone": "+6281234567890",
            "whatsapp_link": "https://wa.me/6281234567890?text=SOS%20-%20John%20Doe%20butuh%20bantuan.%20Booking%3A%20744b875c",
            "tel_link": "tel:+6281234567890"
        },
        {
            "name": "Posko Kerinci",
            "phone": "+6281298765432",
            "whatsapp_link": "https://wa.me/6281298765432",
            "tel_link": "tel:+6281298765432"
        }
    ],
    "errors": null
}
```

---

### 6.2 Kirim Laporan Potensi Bencana

Pendaki melaporkan potensi bencana yang ditemui di jalur.

**Endpoint:** `POST /api/sos/disaster-report`  
**Auth:** Bearer Token  
**Syarat:** Pendakian aktif

#### Cara Penggunaan:
1. Pendaki menemukan potensi bencana (longsor, pohon tumbang, dll)
2. Isi form: jenis bencana, deskripsi, lokasi
3. Lampirkan foto jika memungkinkan
4. Admin menerima notifikasi dan memverifikasi laporan
5. Jika terverifikasi, admin dapat broadcast peringatan ke semua pendaki

#### Parameter Input:

| Parameter | Tipe | Wajib | Validasi | Keterangan |
|-----------|------|-------|----------|------------|
| potensi_bencana | string | ✅ | max: 255 | Jenis bencana (Longsor, Pohon Tumbang, dll) |
| deskripsi | string | ✅ | max: 2000 | Deskripsi detail |
| lokasi | string | ✅ | max: 255 | Deskripsi lokasi (teks) |
| latitude | numeric | ❌ | -90 s/d 90 | Koordinat lokasi bencana |
| longitude | numeric | ❌ | -180 s/d 180 | Koordinat lokasi bencana |
| lampiran | file | ❌ | jpeg/png/jpg, max: 5MB | Foto bukti |

#### Contoh Request:
```
POST /api/sos/disaster-report
Authorization: Bearer {token}
Content-Type: multipart/form-data

potensi_bencana=Longsor
deskripsi=Terlihat retakan tanah besar di jalur, lebar sekitar 2 meter
lokasi=Antara Pos 2 dan Pos 3, sebelah kanan jalur
latitude=-1.6985
longitude=101.2640
lampiran=@foto_retakan.jpg
```

#### Response Berhasil (201):
```json
{
    "success": true,
    "message": "Laporan bencana berhasil dikirim",
    "data": {
        "id": 3,
        "status": "pending",
        "created_at": "2026-05-12T10:00:00.000Z"
    },
    "errors": null
}
```

#### Status Laporan:
- `pending` — Menunggu verifikasi admin
- `verified` — Diverifikasi admin (valid)
- `broadcast` — Sudah dibroadcast ke pendaki lain
- `resolved` — Masalah sudah ditangani
- `rejected` — Ditolak admin (tidak valid)

---

### 6.3 Lihat Laporan Bencana Saya

Ambil daftar laporan bencana yang pernah dikirim.

**Endpoint:** `GET /api/sos/disaster-reports`  
**Auth:** Bearer Token

#### Cara Penggunaan:
1. Panggil untuk melihat status laporan yang pernah dikirim
2. Cek apakah laporan sudah diverifikasi atau ditolak

#### Parameter Input: Tidak ada

#### Contoh Request:
```
GET /api/sos/disaster-reports
Authorization: Bearer {token}
```

#### Response Berhasil (200):
```json
{
    "success": true,
    "message": "Success",
    "data": [
        {
            "id": 3,
            "potensi_bencana": "Longsor",
            "lokasi": "Antara Pos 2 dan Pos 3",
            "status": "verified",
            "created_at": "2026-05-12T10:00:00.000Z"
        },
        {
            "id": 1,
            "potensi_bencana": "Pohon Tumbang",
            "lokasi": "Sebelum Shelter 1",
            "status": "resolved",
            "created_at": "2026-05-11T14:00:00.000Z"
        }
    ],
    "errors": null
}
```


---

## 7. WebSocket Events (Real-time)

### Cara Menggunakan WebSocket:

1. Inisialisasi Laravel Echo di mobile app
2. Subscribe ke channel yang sesuai
3. Listen event yang dibutuhkan

### Daftar Channel:

| Channel | Siapa yang Subscribe | Keterangan |
|---------|---------------------|------------|
| `private-emergency.{destinasi_id}` | Admin + Pendaki aktif | Pesan darurat |
| `private-sos.{destinasi_id}` | Admin saja | Notifikasi SOS baru |
| `private-sos-chat.{sos_id}` | Pemilik SOS + Admin | Chat real-time |
| `private-tracking.{destinasi_id}` | Admin saja | Update posisi pendaki |

### Daftar Event:

| Event | Channel | Kapan Terjadi |
|-------|---------|---------------|
| `emergency.triggered` | `private-emergency.{did}` | Pendaki kirim pesan darurat |
| `emergency.broadcast` | `private-emergency.{did}` | Admin broadcast peringatan |
| `sos.triggered` | `private-sos.{did}` | Pendaki tekan tombol SOS |
| `sos.message.sent` | `private-sos-chat.{sid}` | Pesan chat dikirim |
| `sos.status.updated` | `private-sos.{did}` | Admin ubah status SOS |
| `tracking.updated` | `private-tracking.{did}` | GPS pendaki terupdate |
| `disaster.reported` | `private-sos.{did}` | Laporan bencana masuk |

---

## 8. Referensi Error

### Error Umum:

| Kode | Response | Penyebab |
|------|----------|----------|
| 401 | `{"message": "Unauthenticated."}` | Token tidak valid/expired |
| 403 | `{"success": false, "message": "Tidak ada pendakian aktif"}` | User belum check-in |
| 404 | `{"success": false, "message": "Resource tidak ditemukan"}` | ID tidak ada di database |
| 409 | `{"success": false, "message": "Sudah check-in di pos ini"}` | Duplikat check-in |
| 422 | `{"success": false, "message": "...", "errors": {...}}` | Validasi gagal |
| 429 | `{"success": false, "message": "Tunggu X detik..."}` | Rate limit / cooldown |

### Contoh Error Validasi (422):
```json
{
    "success": false,
    "message": "The latitude field must be between -90 and 90. (and 1 more error)",
    "data": null,
    "errors": {
        "latitude": ["The latitude field must be between -90 and 90."],
        "longitude": ["The longitude field must be between -180 and 180."]
    }
}
```

---

## 9. Ringkasan Endpoint

| # | Method | Endpoint | Keterangan |
|---|--------|----------|------------|
| 1 | POST | `/api/tracking/gps` | Upload GPS single |
| 2 | POST | `/api/tracking/gps/batch` | Upload GPS batch |
| 3 | GET | `/api/tracking/my-position` | Posisi terakhir |
| 4 | GET | `/api/tracking/posts/{gate_id}` | Daftar pos jalur |
| 5 | POST | `/api/tracking/checkpoint/qr` | Check-in QR |
| 6 | POST | `/api/tracking/checkpoint/gps` | Deteksi kedekatan |
| 7 | POST | `/api/tracking/checkpoint/manual` | Check-in manual |
| 8 | GET | `/api/tracking/progress/{booking_id}` | Progress pendakian |
| 9 | POST | `/api/emergency/trigger` | Kirim pesan darurat |
| 10 | GET | `/api/emergency/active` | Pesan darurat aktif |
| 11 | POST | `/api/sos/trigger` | Tombol panik SOS |
| 12 | GET | `/api/sos/active` | SOS aktif saya |
| 13 | POST | `/api/sos/chat/{sos_id}/send` | Kirim chat SOS |
| 14 | GET | `/api/sos/chat/{sos_id}/messages` | Riwayat chat SOS |
| 15 | GET | `/api/sos/call-options` | Kontak darurat |
| 16 | POST | `/api/sos/disaster-report` | Lapor bencana |
| 17 | GET | `/api/sos/disaster-reports` | Laporan saya |
