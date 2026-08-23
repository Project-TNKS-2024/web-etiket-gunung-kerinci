# E-Tiket Gunung Kerinci

Web E-Tiket Gunung Kerinci adalah aplikasi e-ticketing pendakian untuk Taman Nasional Kerinci Seblat (TNKS). Aplikasi ini dibangun dengan Laravel 11, Blade SSR, Laravel Sanctum untuk API mobile, Laravel Reverb untuk WebSocket real-time, queue worker untuk job/background event, dan Midtrans untuk pembayaran.

## Stack Utama

| Komponen | Teknologi |
|---|---|
| Backend | Laravel 11, PHP 8.2+ |
| Frontend | Blade, Bootstrap, Vite |
| API Mobile | Laravel Sanctum Bearer Token |
| Role & Permission | Spatie Laravel Permission |
| WebSocket | Laravel Reverb, Laravel Echo, Pusher JS protocol |
| Queue | Laravel Queue database driver |
| Database | MySQL |
| Payment | Midtrans |
| Data wilayah | wilayah.id / JSON wilayah |

## Dokumentasi Terkait

Baca dokumen berikut untuk detail fitur dan API:

| File | Isi |
|---|---|
| `docs/api.md` | Dokumentasi API dasar: auth, profile, domisili, format response |
| `docs/dokumentasi-api-emergency-tracking-sos.md` | Dokumentasi API emergency, tracking, SOS, chat, laporan bencana |
| `docs/plan-websocket.md` | Rencana dan konfigurasi Laravel Reverb, channel, event, queue, deployment |
| `docs/plan-emergency-tracking-sos.md` | Rencana fitur pesan darurat, pelacakan jejak, SOS, database, API |
| `docs/20260511-phase6-deployment.md` | Catatan deployment, Supervisor, Nginx WebSocket proxy, test broadcast |

## Prasyarat Lokal

Pastikan sudah terpasang:

- PHP 8.2 atau lebih baru
- Composer
- Node.js dan npm
- MySQL atau MariaDB
- Ekstensi PHP umum untuk Laravel: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `ctype`, `json`, `tokenizer`, `xml`, `curl`

## Instalasi Pertama Kali

Clone repository lalu masuk ke folder project:

```bash
git clone <repository-url>
cd web-etiket-gunung-kerinci
```

Install dependency PHP dan JavaScript:

```bash
composer install
npm install
```

Buat file environment dan application key:

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`, minimal sesuaikan database:

```env
APP_NAME="E-Tiket Gunung Kerinci"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=etiket_gunung_kerinci
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database
BROADCAST_CONNECTION=reverb
```

Jalankan migrasi dan seeder:

```bash
php artisan migrate
php artisan db:seed
```

Jika ada perubahan cache konfigurasi atau `.env`, bersihkan cache:

```bash
php artisan optimize:clear
```

## Konfigurasi Reverb Lokal

Project ini menggunakan Laravel Reverb untuk fitur real-time seperti pesan darurat, SOS, SOS chat, dan tracking. Pastikan variabel berikut ada di `.env`:

```env
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=local-dev-app
REVERB_APP_KEY=local-dev-key
REVERB_APP_SECRET=local-dev-secret
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

Untuk production, gunakan key dan secret acak yang aman. Jangan gunakan nilai lokal di atas untuk server production.

## Cara Menjalankan Project Lokal

Untuk development lengkap, gunakan beberapa terminal terpisah.

### Terminal 1: Laravel HTTP Server

Jalankan aplikasi web Laravel:

```bash
php artisan serve
```

Default URL:

```text
http://127.0.0.1:8000
```

Gunakan terminal ini untuk mengakses halaman public, auth, user dashboard, admin panel, dan API.

### Terminal 2: Vite Dev Server

Jalankan Vite agar asset frontend development aktif:

```bash
npm run dev
```

Vite dipakai untuk bundle asset Laravel/Vite, termasuk client JavaScript untuk Echo/Reverb jika view menggunakan `@vite`.

### Terminal 3: Laravel Reverb WebSocket Server

Jalankan WebSocket server:

```bash
php artisan reverb:start
```

Untuk debugging koneksi dan event real-time, gunakan:

```bash
php artisan reverb:start --debug
```

Reverb lokal biasanya berjalan di:

```text
http://127.0.0.1:8080
```

Jika browser/admin panel tidak menerima event real-time, cek terminal Reverb ini terlebih dahulu.

### Terminal 4: Queue Worker

Jalankan worker agar job queue dan broadcast event diproses:

```bash
php artisan queue:work
```

Atau eksplisit menggunakan database queue driver:

```bash
php artisan queue:work database --sleep=3 --tries=3
```

Jika broadcast event dikirim tetapi tidak muncul di browser, penyebab paling umum adalah queue worker belum berjalan.

### Terminal Opsional: Test Broadcast

Jika command tersedia di branch ini, kirim event test:

```bash
php artisan test:broadcast --destinasi=1
```

Command ini dipakai untuk memverifikasi tiga hal:

- Reverb server berjalan
- Queue worker memproses job broadcast
- Admin panel menerima event melalui Laravel Echo

## Alur Development Harian

Jalankan perintah berikut saat mulai kerja:

```bash
php artisan optimize:clear
php artisan serve
```

Di terminal lain:

```bash
npm run dev
```

Jika sedang mengerjakan fitur real-time, jalankan juga:

```bash
php artisan reverb:start --debug
php artisan queue:work database --sleep=3 --tries=3
```

Jika hanya mengerjakan halaman Blade biasa dan tidak memakai asset Vite atau WebSocket, `php artisan serve` saja biasanya cukup.

## API

Base path API:

```text
/api
```

API menggunakan Bearer Token dari Laravel Sanctum:

```http
Authorization: Bearer {token}
Accept: application/json
```

Format response standar:

```json
{
  "success": true,
  "message": "Success message",
  "data": {},
  "errors": null
}
```

Endpoint auth utama:

| Method | Endpoint | Keterangan |
|---|---|---|
| POST | `/api/register` | Register user |
| POST | `/api/login` | Login dan ambil token |
| POST | `/api/logout` | Logout dan hapus token aktif |
| POST | `/api/forgot-password` | Kirim link reset password |
| POST | `/api/reset-password` | Reset password |

Endpoint emergency, tracking, dan SOS tersedia di `docs/dokumentasi-api-emergency-tracking-sos.md`.

## Fitur Real-Time

Fitur real-time menggunakan Laravel Reverb dan Laravel Echo. Channel utama:

| Channel | Pengguna | Fungsi |
|---|---|---|
| `private-emergency.{destinasi_id}` | Admin dan pendaki aktif | Pesan darurat |
| `private-sos.{destinasi_id}` | Admin | Notifikasi SOS baru |
| `private-sos-chat.{sos_id}` | Pemilik SOS dan admin | Chat SOS real-time |
| `private-tracking.{destinasi_id}` | Admin | Update posisi pendaki |

Event utama:

| Event | Fungsi |
|---|---|
| `emergency.triggered` | Pendaki mengirim pesan darurat |
| `emergency.broadcast` | Admin broadcast peringatan |
| `sos.triggered` | Pendaki menekan tombol SOS |
| `sos.message.sent` | Pesan chat SOS dikirim |
| `sos.status.updated` | Admin mengubah status SOS |
| `tracking.updated` | Posisi GPS pendaki berubah |
| `disaster.reported` | Laporan potensi bencana masuk |

## Queue

Project memakai queue untuk job asynchronous, termasuk broadcast event. Untuk lokal, pastikan `.env` berisi:

```env
QUEUE_CONNECTION=database
```

Pastikan tabel queue sudah ada:

```bash
php artisan migrate
```

Perintah penting:

```bash
php artisan queue:work database --sleep=3 --tries=3
php artisan queue:failed
php artisan queue:retry all
php artisan queue:restart
```

Gunakan `queue:restart` setelah deploy atau setelah perubahan kode job/event agar worker memuat kode terbaru.

## Build Production

Untuk membuat asset production:

```bash
npm run build
```

Untuk optimasi Laravel production:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

Jangan jalankan cache production jika `.env` masih sering berubah. Setelah mengubah `.env`, jalankan ulang cache yang relevan atau gunakan:

```bash
php artisan optimize:clear
```

## Deployment Reverb dan Queue dengan Supervisor

File konfigurasi deployment tersedia di folder `deploy/`:

| File | Fungsi |
|---|---|
| `deploy/supervisor-reverb.conf` | Menjalankan `php artisan reverb:start` sebagai service |
| `deploy/supervisor-queue-worker.conf` | Menjalankan `php artisan queue:work database` sebagai service |
| `deploy/nginx-websocket.conf` | Nginx proxy untuk WebSocket Reverb |
| `deploy/deploy.sh` | Script deployment production |

Install Supervisor config di server:

```bash
sudo cp deploy/supervisor-reverb.conf /etc/supervisor/conf.d/reverb.conf
sudo cp deploy/supervisor-queue-worker.conf /etc/supervisor/conf.d/queue-worker.conf
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start reverb
sudo supervisorctl start queue-worker:*
sudo supervisorctl status
```

Tambahkan isi `deploy/nginx-websocket.conf` ke dalam block `server { }` Nginx, lalu reload:

```bash
sudo nginx -t
sudo systemctl reload nginx
```

Contoh `.env` production untuk Reverb di belakang Nginx HTTPS:

```env
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=gk-prod-xxxxxxxx
REVERB_APP_KEY=gk-prod-key-xxxxxxxx
REVERB_APP_SECRET=gk-prod-secret-xxxxxxxx
REVERB_HOST="yourdomain.com"
REVERB_PORT=443
REVERB_SCHEME=https

REVERB_SERVER_HOST=127.0.0.1
REVERB_SERVER_PORT=8080

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"

QUEUE_CONNECTION=database
```

## Troubleshooting

| Masalah | Penyebab Umum | Solusi |
|---|---|---|
| Halaman tidak bisa dibuka | Laravel server belum jalan | Jalankan `php artisan serve` |
| Asset tidak update | Vite belum jalan atau build lama | Jalankan `npm run dev` atau `npm run build` |
| Event real-time tidak masuk | Reverb belum jalan | Jalankan `php artisan reverb:start --debug` |
| Event terkirim tapi tidak diterima | Queue worker belum jalan | Jalankan `php artisan queue:work database --sleep=3 --tries=3` |
| Private channel 403 | Auth/role/channel authorization gagal | Cek token Sanctum, session login, role, dan `routes/channels.php` |
| Config `.env` tidak berubah | Config cache masih lama | Jalankan `php artisan optimize:clear` |
| Job gagal | Error validasi/kode/job dependency | Cek `php artisan queue:failed` dan log Laravel |

Log Laravel berada di:

```text
storage/logs/laravel.log
```

## Testing

Jalankan test Laravel:

```bash
php artisan test
```

Setelah mengubah frontend asset, validasi build:

```bash
npm run build
```

Setelah mengubah route/controller, cek route:

```bash
php artisan route:list
```

## Catatan Keamanan

- Jangan commit `.env`.
- Jangan hard-code credential Midtrans, Reverb secret, database, Google OAuth, atau token lain.
- Semua API mobile yang butuh login harus memakai `auth:sanctum`.
- Semua form POST/PUT/PATCH/DELETE Blade harus memakai `@csrf`.
- Upload file harus divalidasi MIME type dan ukuran file.
- Jangan gunakan GET untuk aksi destruktif.
