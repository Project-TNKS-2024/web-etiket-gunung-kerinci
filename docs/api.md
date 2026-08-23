# Dokumentasi API - E-Tiket Gunung Kerinci

## Informasi Umum

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
