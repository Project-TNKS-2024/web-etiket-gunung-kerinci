# Dokumentasi API - E-Tiket Gunung Kerinci

## Informasi Umum

### Kontrak Waktu WIB

Satu pengaturan backend, `APP_TIMEZONE=Asia/Jakarta`, menentukan zona aplikasi
dan offset sesi koneksi MySQL/MariaDB. Tidak diperlukan `DB_TIMEZONE` terpisah.
Timestamp aplikasi pada response HTTP/broadcast menggunakan ISO-8601 dengan
offset `+07:00`, misalnya `2026-10-20T21:00:00+07:00`.

Mobile menampilkan komponen tanggal dan jam response tersebut secara langsung,
tanpa menambah tujuh jam atau mengonversi UTC. Timestamp response bertanda `Z`
atau zona lain berarti backend belum memenuhi kontrak; jangan dilabeli WIB.
Backend tetap menerima input lama bertanda `Z` dan menormalisasikannya sebelum
penyimpanan. Klien baru mengirim `recorded_at` dengan offset `+07:00`.

Tanggal kalender tanpa jam dan angka epoch tetap mempertahankan semantik aslinya.
Epoch antrean offline menyatakan waktu kejadian; tidak ditambah tujuh jam atau
diganti waktu sinkronisasi. Epoch cuaca dari penyedia eksternal bukan timestamp
ISO milik aplikasi dan tetap diformat sesuai jenis nilainya.

### Base URL
```
/api
```

### Autentikasi
API ini menggunakan **Laravel Sanctum** untuk autentikasi berbasis token. Untuk endpoint yang memerlukan autentikasi, sertakan token di header:

```
Authorization: Bearer {token}
```

### Format Response

Semua response API menggunakan format standar:

**Success Response:**
```json
{
  "success": true,
  "message": "Success message",
  "data": {...},
  "errors": null
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "Error message",
  "data": null,
  "errors": {...}
}
```

---

## 1. Authentication Endpoints

### 1.1 Register
Mendaftarkan user baru dengan email dan password.

- **URL:** `/api/register`
- **Method:** `POST`
- **Auth Required:** No
- **Route Name:** `api.auth.register`

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Validasi:**
- `email`: required, string, email, max:255, unique di tabel users
- `password`: required, string, min:8, confirmed

**Success Response (201):**
```json
{
  "success": true,
  "message": "Registrasi berhasil. Silakan cek email untuk verifikasi.",
  "data": {
    "user": {
      "id": 1,
      "email": "user@example.com",
      "gauth_type": "manual",
      "email_verified_at": null,
      "created_at": "2026-04-14T07:00:00.000000Z",
      "updated_at": "2026-04-14T07:00:00.000000Z"
    },
    "token": "1|abcdefghijklmnopqrstuvwxyz..."
  },
  "errors": null
}
```

**Error Response (422):**
```json
{
  "success": false,
  "message": "Validasi gagal",
  "data": null,
  "errors": {
    "email": ["The email has already been taken."],
    "password": ["The password confirmation does not match."]
  }
}
```

**Error Response (409):**
```json
{
  "success": false,
  "message": "Akun dengan email ini sudah terdaftar.",
  "data": null,
  "errors": []
}
```

**Error Response (500):**
```json
{
  "success": false,
  "message": "Gagal mengirim email verifikasi. Silakan coba lagi nanti.",
  "data": null,
  "errors": {
    "mail_error": "Error message detail"
  }
}
```

---

### 1.2 Login
Login dengan email dan password untuk mendapatkan token autentikasi.

- **URL:** `/api/login`
- **Method:** `POST`
- **Auth Required:** No
- **Route Name:** `api.auth.login`

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Validasi:**
- `email`: required, email
- `password`: required

**Success Response (200):**
```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "user": {
      "id": 1,
      "email": "user@example.com",
      "email_verified_at": "2026-04-14T07:00:00.000000Z",
      "created_at": "2026-04-14T07:00:00.000000Z",
      "updated_at": "2026-04-14T07:00:00.000000Z"
    },
    "token": "2|abcdefghijklmnopqrstuvwxyz..."
  },
  "errors": null
}
```

**Error Response (422):**
```json
{
  "success": false,
  "message": "Validasi gagal",
  "data": null,
  "errors": {
    "email": ["The email field is required."]
  }
}
```

**Error Response (401):**
```json
{
  "success": false,
  "message": "Email atau password salah",
  "data": null,
  "errors": null
}
```

---

### 1.3 Logout
Logout dan menghapus token autentikasi user saat ini.

- **URL:** `/api/logout`
- **Method:** `POST`
- **Auth Required:** Yes (Bearer Token)
- **Route Name:** `api.auth.logout`

**Request Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Logout berhasil",
  "data": null,
  "errors": null
}
```

---

### 1.4 Forgot Password
Mengirim link reset password ke email user.

- **URL:** `/api/forgot-password`
- **Method:** `POST`
- **Auth Required:** No
- **Route Name:** `api.auth.password.forgot`

**Request Body:**
```json
{
  "email": "user@example.com"
}
```

**Validasi:**
- `email`: required, email, exists di tabel users

**Success Response (200):**
```json
{
  "success": true,
  "message": "Link reset password sudah dikirim ke email",
  "data": null,
  "errors": null
}
```

**Error Response (422):**
```json
{
  "success": false,
  "message": "Validasi gagal",
  "data": null,
  "errors": {
    "email": ["The selected email is invalid."]
  }
}
```

---

### 1.5 Reset Password
Reset password menggunakan token yang dikirim via email.

- **URL:** `/api/reset-password`
- **Method:** `POST`
- **Auth Required:** No
- **Route Name:** `api.auth.password.reset`

**Request Body:**
```json
{
  "token": "abc123def456...",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

**Validasi:**
- `token`: required, string
- `password`: required, string, min:8, confirmed

**Success Response (200):**
```json
{
  "success": true,
  "message": "Password berhasil direset, silakan login.",
  "data": null,
  "errors": null
}
```

**Error Response (404):**
```json
{
  "success": false,
  "message": "Token tidak valid",
  "data": null,
  "errors": null
}
```

---

### 1.6 Email Verification Notice
Memeriksa status verifikasi email user.

- **URL:** `/api/email/verify/notice`
- **Method:** `GET`
- **Auth Required:** Yes (Bearer Token)
- **Route Name:** `api.auth.email.notice`

**Request Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Email sudah diverifikasi.",
  "data": null,
  "errors": null
}
```

**Error Response (403):**
```json
{
  "success": false,
  "message": "Email belum diverifikasi",
  "data": null,
  "errors": null
}
```

---

### 1.7 Resend Email Verification
Mengirim ulang email verifikasi.

- **URL:** `/api/email/resend`
- **Method:** `POST`
- **Auth Required:** Yes (Bearer Token)
- **Route Name:** `api.auth.email.resend`

**Request Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Email verifikasi telah dikirim ulang.",
  "data": null,
  "errors": null
}
```

---

### 1.8 Verify Email
Memverifikasi email user melalui link verifikasi.

- **URL:** `/api/email/verify/{id}/{hash}`
- **Method:** `GET`
- **Auth Required:** Yes (Bearer Token)
- **Route Name:** `api.auth.email.verify`

**URL Parameters:**
- `id`: User ID
- `hash`: Hash verifikasi

**Request Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Email berhasil diverifikasi.",
  "data": null,
  "errors": null
}
```

---

### 1.9 Google OAuth Redirect
Mendapatkan URL redirect untuk login dengan Google.

- **URL:** `/api/auth/google/redirect`
- **Method:** `GET`
- **Auth Required:** No
- **Route Name:** `api.auth.google.redirect`

**Success Response (200):**
```json
{
  "success": true,
  "message": "Redirect ke Google",
  "data": {
    "redirect_url": "https://accounts.google.com/o/oauth2/auth?..."
  },
  "errors": null
}
```

---

### 1.10 Google OAuth Callback
Menangani callback dari Google setelah user berhasil autentikasi.

- **URL:** `/api/auth/google/callback`
- **Method:** `GET`
- **Auth Required:** No
- **Route Name:** `api.auth.google.callback`

**Success Response (200):**
```json
{
  "success": true,
  "message": "Login dengan Google berhasil",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@gmail.com",
      "gauth_type": "google",
      "gauth_id": "123456789",
      "email_verified_at": "2026-04-14T07:00:00.000000Z"
    },
    "token": "3|abcdefghijklmnopqrstuvwxyz..."
  },
  "errors": null
}
```

**Error Response (400):**
```json
{
  "success": false,
  "message": "Login dengan Google gagal",
  "data": null,
  "errors": "Error message detail"
}
```

---

## 2. Profile Endpoints

### 2.1 Get Biodata
Mengambil data biodata pendaki user yang sedang login.

- **URL:** `/api/profile/getbiodata`
- **Method:** `GET`
- **Auth Required:** Yes (Bearer Token)
- **Route Name:** `api.profile.getbiodata`

**Request Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Berhasil mengambil biodata",
  "data": {
    "id": 1,
    "nik": "1234567890123456",
    "kenegaraan": "ID",
    "first_name": "John",
    "last_name": "Doe",
    "lampiran_identitas": "identitas_1_1234567890.jpg",
    "no_hp": "+62 81234567890",
    "jenis_kelamin": "l",
    "tanggal_lahir": "1990-01-01",
    "provinsi": 14,
    "kabupaten": 1402,
    "kec": 140201,
    "desa": 14020101,
    "verified": "verified",
    "verified_at": "2026-04-14T07:00:00.000000Z",
    "created_at": "2026-04-14T07:00:00.000000Z",
    "updated_at": "2026-04-14T07:00:00.000000Z"
  },
  "errors": null
}
```

---

### 2.2 Update Biodata
Memperbarui atau membuat biodata pendaki user.

- **URL:** `/api/profile/updatebiodata`
- **Method:** `POST`
- **Auth Required:** Yes (Bearer Token)
- **Route Name:** `api.profile.updatebiodata`

**Request Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body (Form Data):**
```
firstName: John
lastName: Doe
lampiran_identitas: [file] (jpg, jpeg, png, pdf, max 548KB)
kewarganegaraan: ID
nik: 1234567890123456
nomor_telepon: 081234567890
telp_country: +62
jenis_kelamin: l
tanggal_lahir: 1990-01-01
provinsi: 14 (nullable, hanya untuk WNI)
kabupaten_kota: 1402 (nullable, hanya untuk WNI)
kecamatan: 140201 (nullable, hanya untuk WNI)
desa_kelurahan: 14020101 (nullable, hanya untuk WNI)
```

**Validasi:**
- `firstName`: required, string, max:255
- `lastName`: nullable, string, max:255
- `lampiran_identitas`: required, file, mimes:jpg,jpeg,png,pdf, max:548KB
- `kewarganegaraan`: required, string
- `nik`: required, string, min:6, max:16, alpha_num
- `nomor_telepon`: required, numeric
- `telp_country`: required, string, max:5
- `jenis_kelamin`: required, in:l,p
- `tanggal_lahir`: required, date, before:today
- `provinsi`: nullable, numeric (untuk WNI)
- `kabupaten_kota`: nullable, numeric (untuk WNI)
- `kecamatan`: nullable, numeric (untuk WNI)
- `desa_kelurahan`: nullable, numeric (untuk WNI)

**Success Response (200):**
```json
{
  "success": true,
  "message": "Berhasil mengubah data",
  "data": {
    "id": 1,
    "nik": "1234567890123456",
    "kenegaraan": "ID",
    "first_name": "John",
    "last_name": "Doe",
    "lampiran_identitas": "identitas_1_1234567890.jpg",
    "no_hp": "+62 81234567890",
    "jenis_kelamin": "l",
    "tanggal_lahir": "1990-01-01",
    "provinsi": 14,
    "kabupaten": 1402,
    "kec": 140201,
    "desa": 14020101,
    "verified": "pending",
    "verified_at": null,
    "created_at": "2026-04-14T07:00:00.000000Z",
    "updated_at": "2026-04-14T07:00:00.000000Z"
  },
  "errors": null
}
```

**Error Response (422):**
```json
{
  "success": false,
  "message": "Validasi gagal",
  "data": null,
  "errors": {
    "firstName": ["The first name field is required."],
    "lampiran_identitas": ["The lampiran identitas must be a file of type: jpg, jpeg, png, pdf."]
  }
}
```

**Error Response (409):**
```json
{
  "success": false,
  "message": "NIK sudah digunakan",
  "data": null,
  "errors": 409
}
```

**Catatan:**
- NIK hanya bisa diubah jika biodata belum diverifikasi
- Nomor HP akan diformat otomatis dengan kode negara
- Jika kewarganegaraan bukan "ID", field provinsi, kabupaten, kecamatan, dan desa akan di-set null
- Status verified akan di-set ke "pending" setelah update

---

### 2.3 Ganti Password
Mengubah password user yang sedang login.

- **URL:** `/api/profile/gantipassword`
- **Method:** `POST`
- **Auth Required:** Yes (Bearer Token)
- **Route Name:** `api.profile.gantipassword`

**Request Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "password_baru": "newpassword123",
  "password_baru_confirmation": "newpassword123"
}
```

**Validasi:**
- `password_baru`: required, string, min:8, confirmed

**Success Response (200):**
```json
{
  "success": true,
  "message": "Password berhasil diperbarui",
  "data": null,
  "errors": null
}
```

**Error Response (422):**
```json
{
  "success": false,
  "message": "Validasi gagal",
  "data": null,
  "errors": {
    "password_baru": ["Password minimal 8 karakter."]
  }
}
```

---

## 3. Domisili Endpoints

Endpoint untuk mengambil data wilayah Indonesia (Provinsi, Kabupaten, Kecamatan, Kelurahan/Desa) dan Negara.

### 3.1 Get Negara
Mengambil daftar semua negara.

- **URL:** `/api/domisili/negara`
- **Method:** `GET`
- **Auth Required:** No

**Success Response (200):**
```json
{
  "success": true,
  "message": "Berhasil",
  "data": [
    {
      "id": "ID",
      "name": "Indonesia"
    },
    {
      "id": "MY",
      "name": "Malaysia"
    }
  ],
  "errors": null
}
```

**Error Response (500):**
```json
{
  "success": false,
  "message": "gagal mengambil data negara",
  "data": null,
  "errors": ""
}
```

---

### 3.2 Get Provinsi
Mengambil daftar semua provinsi di Indonesia.

- **URL:** `/api/domisili/provinsi`
- **Method:** `GET`
- **Auth Required:** No

**Success Response (200):**
```json
{
  "success": true,
  "message": "Berhasil",
  "data": [
    {
      "id": "11",
      "name": "ACEH"
    },
    {
      "id": "12",
      "name": "SUMATERA UTARA"
    },
    {
      "id": "14",
      "name": "RIAU"
    }
  ],
  "errors": null
}
```

---

### 3.3 Get Provinsi By ID
Mengambil detail provinsi berdasarkan ID.

- **URL:** `/api/domisili/provinsi/{id}`
- **Method:** `GET`
- **Auth Required:** No

**URL Parameters:**
- `id`: ID Provinsi (contoh: 14)

**Success Response (200):**
```json
{
  "success": true,
  "message": "Berhasil",
  "data": [
    {
      "id": "14",
      "name": "RIAU"
    }
  ],
  "errors": null
}
```

---

### 3.4 Get Kabupaten By Provinsi ID
Mengambil daftar kabupaten/kota berdasarkan ID provinsi.

- **URL:** `/api/domisili/kabupaten/provinsi/{id}`
- **Method:** `GET`
- **Auth Required:** No

**URL Parameters:**
- `id`: ID Provinsi (contoh: 14)

**Success Response (200):**
```json
{
  "success": true,
  "message": "Berhasil",
  "data": [
    {
      "id": "1401",
      "provinsi_id": "14",
      "name": "KABUPATEN KUANTAN SINGINGI"
    },
    {
      "id": "1402",
      "provinsi_id": "14",
      "name": "KABUPATEN INDRAGIRI HULU"
    }
  ],
  "errors": null
}
```

---

### 3.5 Get Kabupaten By ID
Mengambil detail kabupaten/kota berdasarkan ID.

- **URL:** `/api/domisili/kabupaten/{id}`
- **Method:** `GET`
- **Auth Required:** No

**URL Parameters:**
- `id`: ID Kabupaten (contoh: 1402)

**Success Response (200):**
```json
{
  "success": true,
  "message": "Berhasil",
  "data": [
    {
      "id": "1402",
      "provinsi_id": "14",
      "name": "KABUPATEN INDRAGIRI HULU"
    }
  ],
  "errors": null
}
```

---

### 3.6 Get Kecamatan By Kabupaten ID
Mengambil daftar kecamatan berdasarkan ID kabupaten.

- **URL:** `/api/domisili/kecamatan/kabupaten/{id}`
- **Method:** `GET`
- **Auth Required:** No

**URL Parameters:**
- `id`: ID Kabupaten (contoh: 1402)

**Success Response (200):**
```json
{
  "success": true,
  "message": "Berhasil",
  "data": [
    {
      "id": "140201",
      "kabupaten_id": "1402",
      "name": "RENGAT"
    },
    {
      "id": "140202",
      "kabupaten_id": "1402",
      "name": "RENGAT BARAT"
    }
  ],
  "errors": null
}
```

---

### 3.7 Get Kecamatan By ID
Mengambil detail kecamatan berdasarkan ID.

- **URL:** `/api/domisili/kecamatan/{id}`
- **Method:** `GET`
- **Auth Required:** No

**URL Parameters:**
- `id`: ID Kecamatan (contoh: 140201)

**Success Response (200):**
```json
{
  "success": true,
  "message": "Berhasil",
  "data": [
    {
      "id": "140201",
      "kabupaten_id": "1402",
      "name": "RENGAT"
    }
  ],
  "errors": null
}
```

---

### 3.8 Get Kelurahan By Kecamatan ID
Mengambil daftar kelurahan/desa berdasarkan ID kecamatan.

- **URL:** `/api/domisili/kelurahan/kecamatan/{id}`
- **Method:** `GET`
- **Auth Required:** No

**URL Parameters:**
- `id`: ID Kecamatan (contoh: 140201)

**Success Response (200):**
```json
{
  "success": true,
  "message": "Berhasil",
  "data": [
    {
      "id": "14020101",
      "kecamatan_id": "140201",
      "name": "KAMPUNG DALAM"
    },
    {
      "id": "14020102",
      "kecamatan_id": "140201",
      "name": "PULAU RENGAT"
    }
  ],
  "errors": null
}
```

---

### 3.9 Get Kelurahan By ID
Mengambil detail kelurahan/desa berdasarkan ID.

- **URL:** `/api/domisili/kelurahan/{id}`
- **Method:** `GET`
- **Auth Required:** No

**URL Parameters:**
- `id`: ID Kelurahan (contoh: 14020101)

**Success Response (200):**
```json
{
  "success": true,
  "message": "Berhasil",
  "data": [
    {
      "id": "14020101",
      "kecamatan_id": "140201",
      "name": "KAMPUNG DALAM"
    }
  ],
  "errors": null
}
```

---

## 4. Error Handling

### 4.1 API Not Found (Fallback)
Jika endpoint tidak ditemukan, akan mengembalikan response error.

- **Route Name:** `api.error`

**Error Response (404):**
```json
{
  "success": false,
  "message": "API tidak tersedia",
  "errors": {
    "path": "api/invalid-endpoint"
  }
}
```

---

## 5. Catatan Penting

### 5.1 Autentikasi
- Gunakan Laravel Sanctum untuk autentikasi
- Token diperoleh setelah login atau register
- Token harus disertakan di header `Authorization: Bearer {token}` untuk endpoint yang memerlukan autentikasi

### 5.2 Data Domisili
- Data domisili (negara, provinsi, kabupaten, kecamatan, kelurahan) disimpan dalam file JSON di `public/assets/json/`
- Data di-cache secara permanen menggunakan `Cache::rememberForever()`
- Tidak memerlukan autentikasi untuk mengakses data domisili

### 5.3 Upload File
- Endpoint update biodata menerima upload file identitas
- Format yang diterima: jpg, jpeg, png, pdf
- Ukuran maksimal: 548KB
- File akan disimpan dengan nama format: `identitas_{user_id}_{timestamp}.{ext}`

### 5.4 Email Verification
- Setelah register, user akan menerima email verifikasi
- User harus memverifikasi email melalui link yang dikirim
- Beberapa fitur mungkin memerlukan email yang sudah diverifikasi

### 5.5 OAuth Google
- Mendukung login dengan Google OAuth
- User yang login dengan Google akan otomatis terverifikasi emailnya
- Password akan di-generate secara random untuk user Google

### 5.6 Biodata Verification
- Setelah update biodata, status verified akan menjadi "pending"
- Admin akan memverifikasi biodata secara manual
- NIK yang sudah diverifikasi tidak bisa diubah lagi

---

## 6. HTTP Status Codes

| Code | Deskripsi |
|------|-----------|
| 200  | OK - Request berhasil |
| 201  | Created - Resource berhasil dibuat |
| 400  | Bad Request - Request tidak valid |
| 401  | Unauthorized - Autentikasi gagal |
| 403  | Forbidden - Tidak memiliki akses |
| 404  | Not Found - Resource tidak ditemukan |
| 409  | Conflict - Konflik data (misal: email/NIK sudah ada) |
| 422  | Unprocessable Entity - Validasi gagal |
| 500  | Internal Server Error - Error di server |

---

## 7. Integrasi Pihak Ketiga

### 7.1 Midtrans
API ini terintegrasi dengan Midtrans untuk payment gateway (implementasi ada di controller lain).

### 7.2 Wilayah.id
Data wilayah Indonesia menggunakan format dari wilayah.id.

---

**Dokumentasi ini dibuat pada:** 14 April 2026  
**Versi API:** 1.0  
**Framework:** Laravel 11 dengan Sanctum


### Peta posisi per pos

`GET /api/tracking/posts/{gate_id}` dan `GET /api/tracking/v2/posts/{gate_id}`
menyertakan `map_image_url` (URL absolut, atau `null` bila belum tersedia).
Gambar mengikuti pos yang dipilih, bukan pos tujuan berikutnya. Mobile menampilkan
peta utuh dan membuka tampilan zoom ketika diketuk.

`mountain_status` pada kedua endpoint diisi dari status destinasi melalui relasi
gate, bukan dari nilai tersimpan per pos. Semua pos dalam satu destinasi memakai
label yang sama dengan Beranda; `null` berarti status belum tersedia.

Sepuluh peta PNG jalur Kersik Tuo tersedia di
`public/images/trail-maps/kersik-tuo/`. Jalankan migrasi
`85_gk_posts_add_map_image_path.php`, lalu `TrailPostSeeder` untuk memasangkannya.
Pada database kosong, jalankan `destinasi_gate` terlebih dahulu. Seeder mengikuti
10 titik pada gambar; Batu Gantung dan Bayangan Shelter 2 lama dinonaktifkan
tanpa menghapus ID atau riwayat. Data jarak dan durasi demo Shelter 3–Tugu Yudha
menggabungkan dua segmen lama melalui Batu Gantung. Jalur Solok Selatan belum
memiliki peta dan mengembalikan `map_image_url: null`.


## Riwayat dan pembatalan tiket (18 Oktober 2026)

`GET /api/mytiket` mempertahankan dua kelompok: Draft (0–3 dan 9 dibatalkan), Tiket Saya (4–8). Status 7–8 berarti pendakian selesai; tagihan tambahan tetap berdiri sendiri. Respons booking menambahkan `cancelled_at`, `cancelled_by`, `cancellation_reason`, `payment_deadline`, `ticket_notice`, `ticket_notice_type` (warning/error/null), dan `can_continue`. Kode pemesanan berasal dari `unique_code`. Tenggat pembayaran tanpa bukti mengikuti batas 24 jam setelah finalisasi; bukti pending menampilkan menunggu verifikasi.

`DELETE /api/booking/{id}` tetap khusus pemilik dan status 0–3; body opsional `reason` (maksimal 500 karakter). Pembatalan menyimpan status 9 beserta alasan, waktu, pelaku, anggota, dan bukti pembayaran. Pembatalan ulang mengembalikan keberhasilan yang sama; status yang tidak memenuhi syarat menghasilkan 409, booking bukan milik pengguna menghasilkan 404.

Admin dengan izin `edit-booking` dapat mengirim `POST /admin/destinasi/booking/cancel` dengan `booking_id` dan `reason` wajib. Pembatalan hanya sebelum pembayaran dikonfirmasi (0–3); tidak ada perubahan aturan refund atau pembatalan pendaki yang sudah check-in. Booking batal tidak dapat dilanjutkan/dikonfirmasi dan tidak memakan kuota. Daftar admin menyediakan filter serta detail riwayat pembatalan.


## Ringkasan kartu Beranda (18 Oktober 2026)

`GET /api/beranda` mempertahankan field lama dan menambah:

- `status_gunung`: `{kode, label}` dari destinasi Kerinci, atau null jika tidak tersedia/di luar kode 0–4. Bukan fallback status aman/bahaya.
- `ringkasan_pendaki`: `{sedang_mendaki, total_tercatat, cakupan: "seluruh_riwayat"}`. Angka pertama adalah anggota booking dengan status terakhir check-in; angka kedua mencakup anggota yang telah check-in atau checkout. Keduanya menghitung kunjungan per anggota booking Kerinci status 6–8, bukan kuota, orang unik lintas booking, atau statistik hari ini. Pembatalan/draft/no-show tidak dihitung.
- `ringkasan_diperbarui_pada`: waktu respons ISO 8601.
- `cuaca_diamati_pada`: Unix timestamp pengamatan dari provider, atau null. Bukan waktu request aplikasi.

Cuaca berbahasa Indonesia, cache sukses 5 menit; jumlah pendaki dan status tidak ikut dicache. Cuaca gagal tidak menghilangkan angka/status: `cuaca` null, bagian lain tetap dikirim. Nol berarti perhitungan berhasil dan hasilnya kosong. Key cuaca dibaca dari `WEATHER_API_KEY` melalui konfigurasi services; koordinat opsional `WEATHER_COORDINATES`. Log gagal tidak memuat URL, key, atau exception request. Key lama yang pernah masuk Git/log perlu dirotasi oleh pemilik akun provider. Tidak ada migration baru.

Jika PHP lokal belum memiliki trust store CA, `WEATHER_CA_BUNDLE` dapat menunjuk ke bundle PEM tepercaya milik lingkungan (path privat; jangan commit sertifikat lokal). Verifikasi HTTPS tetap aktif. Cache cuaca hanya menyimpan respons berhasil selama 300 detik.


## Identitas tiket untuk checkpoint (21 Oktober 2026)

Mobile memakai `GET /api/tracking/v2/posts/{gate_id}` agar konfigurasi puncak dan segmen turun tersedia. Untuk QR dan manual v2, kirim `pendaki_id` UUID dari booking yang sedang ditampilkan, termasuk ketika mencatat diri sendiri dan saat sinkronisasi offline. Server mencari pendaki pemanggil pada booking target itu; hak mewakili anggota tetap hanya untuk ketua booking. Parameter tetap opsional demi kompatibilitas klien lama, tetapi klien baru tidak boleh mengandalkan pilihan booking aktif pertama. Pembatalan menentukan booking dari log yang dibatalkan dan tetap memverifikasi akses.

`POST /api/tracking/checkpoint/gps` juga menerima `pendaki_id` UUID opsional untuk memilih pendakian pemanggil sebelum mencari pos terdekat. Target GPS wajib merupakan identitas pemanggil sendiri. Mobile memvalidasi `pendaki_id`, pos, fase, dan `client_event_id` pada respons sukses sebelum menyimpan centang lokal; respons yang tidak cocok tidak dianggap absen berhasil.

## GPS pendamping checkpoint — 25 Oktober 2026

Pembaruan 25 Oktober 2026: QR/manual checkpoint v2 menyimpan GPS diri yang
berkoordinat lengkap dalam transaksi checkpoint yang sama. Field opsional baru
adalah altitude (-99999.99..99999.99), accuracy (0..9999.99 meter), dan
battery_level (integer 0..100). Replay client_event_id tidak membuat titik kedua.
Kehadiran anggota yang diwakili tidak menghasilkan titik GPS anggota. Mobile baru
tidak perlu mengirim GPS pendamping terpisah. Lihat
`docs/checkpoint-naik-turun-v2-kontrak.md` untuk kontrak, urutan pembaruan, dan
batas data historis. Respons checkpoint serta endpoint GPS mandiri tetap tersedia.

## Kesegaran GPS dan Live Monitoring — 25 Oktober 2026

QR/manual v2 menerima `position_recorded_at` opsional (ISO-8601): waktu GPS diukur,
terpisah dari `recorded_at` kehadiran. Mobile mengukur ulang lokasi pada scan QR,
deteksi radius, dan konfirmasi; hanya menerima umur maksimal 60 detik. Waktu
pengukuran dipertahankan di antrean Room 17 → 18. Klien/antrean lama tanpa field
baru tetap memakai waktu checkpoint sebagai fallback, tanpa menebak waktu GPS asli.

Endpoint GPS tunggal/batch dan position_recorded_at menolak waktu lebih dari lima
menit ke depan. Posisi terakhir pada my-position serta Admin dipilih dari
recorded_at <= waktu server, urut recorded_at terbaru lalu ID terbesar saat waktu
sama. Titik offline lama tetap tersimpan tanpa menggantikan posisi yang lebih baru.
Data historis bertanggal masa depan tidak dihapus, tetapi tidak dipilih sebelum
waktu server mencapainya.

`GET /admin/monitoring` tetap halaman web terautentikasi dan dibatasi penugasan
destinasi. Accept application/json mengembalikan snapshot server_time, hikers,
sos, posts, emergency_count dengan Cache-Control no-store, private. Halaman
memperbarui snapshot setiap 5 detik (tidak menumpuk request; berhenti saat tab
tersembunyi/sesi tidak sah), menjaga posisi/zoom peta, dan memperbarui umur lokasi
setiap 10 detik. Lokasi lebih tua dari 30 menit ditandai kuning, SOS tetap merah.
Kegagalan pembaruan mempertahankan data terakhir dengan keterangan gagal, bukan
menunjukkan keberhasilan baru. Tidak memerlukan WebSocket atau migrasi Backend.

## Catatan anggota checkpoint (18 Oktober 2026)

`POST /api/tracking/v2/member-note` (Sanctum) menerima `pendaki_id` UUID, `post_id`, `phase` (`naik`/`turun`), dan `note` nullable string maksimal 500 karakter. Field `note` wajib ada, nilai kosong mengosongkan catatan. Scope unik: booking aktif + anggota + pos + fase. Catatan tidak membuat log kehadiran. Hanya pemilik catatan atau ketua booking yang dapat menulis; untuk anggota lain, ketua harus sudah check-in pada pos/fase sama. Anggota asing ditolak 403, pos berbeda gate 404, anggota sudah check-in ditolak 409. Penyimpanan diserialisasi dengan lock pendaki yang juga dipakai check-in.

`GET /api/tracking/v2/progress/{booking_id}` menambahkan `member_notes`: daftar `{pendaki_id, post_id, phase, note, updated_at}`. Akses mengikuti keanggotaan/pemilik booking pada endpoint progress yang sudah ada. Catatan disimpan terpisah dari `gk_checkpoint_logs` agar dapat ditulis sebelum kehadiran, dan tetap tersimpan ketika UI menyembunyikannya setelah hijau. Jalankan migration 87 sebelum memakai kode ini. Tidak ada antrean offline baru untuk catatan.

## Status pos dilewati (23 Oktober 2026)

`GET /api/tracking/v2/progress/{booking_id}` menambahkan field `status` dan `is_next` pada setiap item `visits[]`. Nilai `status`: `completed` (sudah check-in), `skipped` (belum check-in, tetapi ada check-in pada kunjungan sesudahnya), atau `upcoming` (belum check-in dan masih di depan). Field lama `completed` tetap ada. Status dihitung dari urutan itinerary naik–turun per pendaki dan booking; tidak ada kolom database baru dan tidak ada log yang dibuat untuk pos yang dilewati.

`next_visit_key` kini menunjuk kunjungan tepat setelah posisi terjauh yang tercatat, bukan pos kosong pertama. Check-in susulan pada pos merah mengubahnya menjadi hijau tanpa memundurkan tujuan. Pembatalan check-in terjauh menghitung ulang status dan tujuan. Pos `skipped` tidak menambah angka `completed` pada `ascent_progress`, `descent_progress`, maupun `overall_progress`.

QR dan manual v2 tetap menerima pos mana pun yang belum selesai pada fase aktif. Pemeriksaan QR terhadap pos pilihan, gate booking, lokasi/radius, hari aktif, identitas, dan kewenangan pengguna tidak berubah. Check-in puncak tetap menjadi syarat fase turun dan pembatalan puncak tetap ditolak selama ada check-in turun.

## Validasi radius dan akses chat SOS — 2026-10-24

- Manual check-in v1/v2 menggunakan radius_meter pos. Lokasi di luar radius menghasilkan HTTP 422 dengan errors.code=CHECKPOINT_OUTSIDE_RADIUS, distance_from_post, dan radius_meter; tidak membuat log. Konfigurasi lokasi/radius tidak valid atau koordinat hilang menghasilkan CHECKPOINT_LOCATION_INVALID (422). Pemeriksaan ada di CheckpointVisitService, termasuk jalur legacy. Check-in manual yang diterima mempunyai is_manual_override=false. Konfirmasi tiket oleh petugas tetap alur terpisah.
- Akses baca/kirim chat dan kanal sos-chat.{sosId} untuk pendaki memakai id_bio pendaki pemicu SOS. Kepemilikan booking saja tidak memberi akses ke SOS anggota lain. Admin tetap harus ditugaskan pada destinasi SOS.
- Pengiriman chat hanya menerima status active, acknowledged, dispatched. Sesi lain (termasuk resolved/false_alarm) ditolak HTTP 409, errors.code=SOS_SESSION_CLOSED. Riwayat tetap dapat dibaca pengguna berwenang.
- Pengiriman mengunci baris SOS dalam transaksi sebelum memeriksa status dan menyimpan teks/gambar. Broadcast dilakukan setelah commit. Penolakan sesi tertutup terjadi sebelum penyimpanan lampiran.
- Tidak ada perubahan format sukses atau migrasi. Data override historis tidak diubah. Pemeriksaan sintaks lulus; tes fungsi belum dijalankan.

## Gambar pos per fase — 2026-10-24

Respons pos v1/v2 menyertakan map_image_url (naik) dan map_image_url_turun (turun). Jalur Kersik Tuo memakai aset bernama <nama-pos>-naik.webp / <nama-pos>-turun.webp. Puncak hanya mempunyai gambar naik; URL turun null. Field URL turun dihitung dari aset yang tersedia, tanpa kolom atau migrasi database baru.

Urutan sumber 0–10 naik dari Kersik Tuo ke puncak; 11–20 turun dari Tugu Yudha ke Kersik Tuo. Pemetaan lengkap: public/images/trail-maps/kersik-tuo/manifest.json. Seeder mengisi map_image_path ke gambar naik; aset lama tetap tersedia untuk kompatibilitas URL sebelumnya.

Mobile memilih URL sesuai fase kunjungan. Cache pos yang menyimpan URL naik dapat menurunkan nama pasangan turun untuk aset baru Kersik Tuo. Rasio tampilan mengikuti dimensi aktual gambar. Tidak ada perubahan skema Room.

## Laporan potensi bencana offline (25 Oktober 2026)

`POST /api/sos/disaster-report` tetap menerima multipart dan Sanctum. Tambahan opsional: `client_event_id` UUID, unik per pengguna, serta `location_recorded_at` tanggal ISO 8601 dengan offset (Mobile mengirim UTC Z; penyimpanan mengikuti WIB). Latitude/longitude tetap merupakan koordinat saat laporan dibuat, bukan lokasi saat antrean dikirim. Jalankan migrasi 89 sebelum menggunakan backend ini.

Request pertama membuat laporan dan mengembalikan HTTP 201. Pengiriman ulang `client_event_id` yang sama oleh pengguna yang sama mengembalikan laporan sebelumnya dengan HTTP 200 tanpa membuat laporan/notifikasi baru; replay diperiksa sebelum persyaratan pendakian aktif. Request baru tetap memerlukan pendakian aktif. Format respons lama tetap `{success,message,data:{id,status,created_at},errors}`. Klien lama boleh tidak mengirim field tambahan; pencegahan duplikat hanya berlaku untuk request dengan ID event.

Mobile menyimpan form yang sudah disubmit, foto pada penyimpanan privat, koordinat, waktu GPS, dan akurasi di Room 19. Worker dengan syarat jaringan tersambung mengirim data tersimpan. Gangguan jaringan, HTTP 401/408/429/5xx dicoba ulang; penolakan lain dipertahankan dengan status dan alasan. Akun lain tidak mengirim antrean pemilik sebelumnya. Tidak ada pengiriman langsung atau pengambilan GPS baru di worker. Admin menerima laporan melalui tabel/alur lama; tidak ada perubahan tampilan Admin.
