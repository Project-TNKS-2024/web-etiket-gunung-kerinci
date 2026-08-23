# Implementation Plan: WebSocket (Laravel Reverb)

> **Project**: Web E-Tiket Gunung Kerinci (TNKS)  
> **Date**: 2026-05-05  
> **Status**: Planning Phase  
> **Depends on**: None (this is the foundation for Pesan Darurat & SOS features)  
> **Stack**: Laravel 11.51.0 / PHP 8.4.20 / Vite 5 / MySQL / VPS Deployment

---

## Table of Contents

1. [Current State Assessment](#1-current-state-assessment)
2. [Architecture Design](#2-architecture-design)
3. [Phase 1: Server-Side Setup](#3-phase-1-server-side-setup)
4. [Phase 2: Client-Side Setup (Web Admin)](#4-phase-2-client-side-setup-web-admin)
5. [Phase 3: Mobile Client Setup (API/Sanctum)](#5-phase-3-mobile-client-setup-apisanctum)
6. [Phase 4: Event & Channel Design](#6-phase-4-event--channel-design)
7. [Phase 5: Queue Worker & Process Management](#7-phase-5-queue-worker--process-management)
8. [Phase 6: VPS Deployment (Nginx + Supervisor)](#8-phase-6-vps-deployment-nginx--supervisor)
9. [Phase 7: Testing & Verification](#9-phase-7-testing--verification)
10. [Channel Authorization Map](#10-channel-authorization-map)
11. [Event Catalog](#11-event-catalog)
12. [Environment Variables Reference](#12-environment-variables-reference)
13. [Troubleshooting Guide](#13-troubleshooting-guide)

---

## 1. Current State Assessment

### What Exists

| Component | Current State | Action Needed |
|-----------|--------------|---------------|
| `BROADCAST_CONNECTION` | `log` (disabled) | Change to `reverb` |
| `config/broadcasting.php` | Does NOT exist | Will be auto-created by `install:broadcasting` |
| `routes/channels.php` | Does NOT exist | Will be auto-created by `install:broadcasting` |
| `app/Events/` | Empty/missing | Create event classes |
| `app/Listeners/` | Empty/missing | Create listener classes |
| Queue driver | `database` (MySQL `jobs` table) | Already works, just need worker process |
| Queue worker | Not running (no Supervisor) | Must configure Supervisor |
| JS client (Echo) | Not installed | Install `laravel-echo` + `pusher-js` |
| Admin JS loading | Static `asset()` + CDN, jQuery, Bootstrap | Add Echo via `@yield('js')` or global include |
| Vite | Configured but only for `resources/css/app.css` + `resources/js/app.js` | Extend for Echo |
| `resources/js/bootstrap.js` | Only Axios configured | Add Echo initialization |
| Redis | Env vars present, not used | Optional: switch queue to Redis for better performance |

### What Does NOT Exist (Must Build from Scratch)

- Broadcasting configuration
- WebSocket server (Reverb)
- Channel authorization logic
- Event classes for real-time features
- Laravel Echo client initialization
- Supervisor process management
- Nginx WebSocket proxy
- Mobile WebSocket client setup

---

## 2. Architecture Design

### Connection Flow

```
┌─────────────────────────────────────────────────────────────────────────┐
│                           VPS (Same Server)                               │
│                                                                          │
│  ┌──────────────┐     ┌──────────────────┐     ┌─────────────────────┐  │
│  │   Nginx      │     │  Laravel App     │     │  Laravel Reverb     │  │
│  │   :80/:443   │────▶│  (PHP-FPM)       │     │  (WebSocket Server) │  │
│  │              │     │                  │     │  127.0.0.1:8080     │  │
│  │  /app/* ─────┼─────┼──────────────────┼────▶│                     │  │
│  └──────────────┘     │  Events dispatch │────▶│  Broadcasts to      │  │
│         ▲             │  via Queue       │     │  connected clients  │  │
│         │             └──────────────────┘     └─────────────────────┘  │
│         │                      │                         │              │
│         │              ┌───────▼────────┐                │              │
│         │              │  Queue Worker  │                │              │
│         │              │  (Supervisor)  │                │              │
│         │              └────────────────┘                │              │
└─────────┼───────────────────────────────────────────────┼──────────────┘
          │                                               │
          │  HTTPS + WSS (port 443)                       │
          │                                               │
┌─────────┼───────────────────────────────────────────────┼──────────────┐
│         │              CLIENTS                          │              │
│  ┌──────┴──────────┐                    ┌──────────────┴────────────┐ │
│  │  Admin Browser   │                    │  Mobile App (Flutter/RN)  │ │
│  │  Laravel Echo    │◀───────────────────│  pusher-channels-flutter  │ │
│  │  (pusher-js)     │   Same WSS conn   │  or laravel-echo (JS)     │ │
│  └──────────────────┘                    └───────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────────┘
```

### Channel Types Used

| Channel Type | Pattern | Use Case | Auth Required |
|-------------|---------|----------|---------------|
| **Private** | `private-emergency.{destinasi_id}` | Emergency alerts per destination | Yes (admin or active hiker) |
| **Private** | `private-sos.{destinasi_id}` | SOS notifications for admins | Yes (admin only) |
| **Private** | `private-sos-chat.{sos_id}` | Two-way SOS chat | Yes (involved hiker or admin) |
| **Private** | `private-tracking.{destinasi_id}` | Live hiker positions for admin | Yes (admin only) |
| **Presence** | `presence-monitoring.{destinasi_id}` | Who's watching the monitoring dashboard | Yes (admin only) |
| **Public** | `public-announcement` | System-wide announcements | No |

---

## 3. Phase 1: Server-Side Setup

### 1.1 Install Laravel Reverb

- [ ] Run `php artisan install:broadcasting` (creates `config/broadcasting.php` + `routes/channels.php`)
- [ ] When prompted, select **Yes** to install Reverb
- [ ] Alternatively, manual install: `composer require laravel/reverb`
- [ ] Run `php artisan reverb:install` (publishes config, adds env vars)
- [ ] Verify `config/broadcasting.php` exists with `reverb` driver configured
- [ ] Verify `config/reverb.php` exists with server settings
- [ ] Verify `routes/channels.php` exists

### 1.2 Configure Environment Variables

- [ ] Update `.env` — set `BROADCAST_CONNECTION=reverb`
- [ ] Verify Reverb env vars are present (auto-added by installer):
  ```env
  REVERB_APP_ID=my-app-id
  REVERB_APP_KEY=my-app-key
  REVERB_APP_SECRET=my-app-secret
  REVERB_HOST="localhost"
  REVERB_PORT=8080
  REVERB_SCHEME=http

  VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
  VITE_REVERB_HOST="${REVERB_HOST}"
  VITE_REVERB_PORT="${REVERB_PORT}"
  VITE_REVERB_SCHEME="${REVERB_SCHEME}"
  ```
- [ ] Generate secure random values for `REVERB_APP_ID`, `REVERB_APP_KEY`, `REVERB_APP_SECRET`
- [ ] Update `.env.example` with placeholder Reverb variables (for team)

### 1.3 Enable Broadcasting Service Provider

- [ ] Verify `BroadcastServiceProvider` is registered (Laravel 11 auto-discovers)
- [ ] Confirm `Broadcast::routes()` is called (for channel auth endpoint `/broadcasting/auth`)
- [ ] For Sanctum API auth: ensure broadcasting auth works with token-based auth (see Phase 3)

### 1.4 Configure Queue (Required for Broadcasting)

- [ ] Verify `QUEUE_CONNECTION=database` in `.env` (already set)
- [ ] Verify `jobs` table migration exists (check `database/migrations/`)
- [ ] Run `php artisan queue:table` if migration doesn't exist
- [ ] Run `php artisan migrate` to ensure `jobs`, `failed_jobs`, `job_batches` tables exist
- [ ] Test queue works: `php artisan queue:work --once` (process single job)

### 1.5 Test Reverb Server Locally

- [ ] Start Reverb: `php artisan reverb:start`
- [ ] Verify output shows "Starting server on 0.0.0.0:8080"
- [ ] Test connection with curl: `curl -i http://127.0.0.1:8080` (should get upgrade required or connection)
- [ ] Start with debug mode: `php artisan reverb:start --debug` (shows all connections/messages)

---

## 4. Phase 2: Client-Side Setup (Web Admin)

### 2.1 Install NPM Packages

- [ ] Run `npm install --save-dev laravel-echo pusher-js`
- [ ] Verify `package.json` updated with both packages
- [ ] Run `npm run build` to verify no conflicts

### 2.2 Configure Laravel Echo in Bootstrap

- [ ] Update `resources/js/bootstrap.js`:
  ```js
  import axios from 'axios';
  window.axios = axios;
  window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

  // Laravel Echo + Reverb WebSocket
  import Echo from 'laravel-echo';
  import Pusher from 'pusher-js';
  window.Pusher = Pusher;

  window.Echo = new Echo({
      broadcaster: 'reverb',
      key: import.meta.env.VITE_REVERB_APP_KEY,
      wsHost: import.meta.env.VITE_REVERB_HOST,
      wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
      wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
      forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
      enabledTransports: ['ws', 'wss'],
  });
  ```

### 2.3 Integrate with Admin Template

The admin panel uses static assets via `asset()` (not Vite). We have two options:

**Option A: Use Vite for admin panel (Recommended)**

- [ ] Update `vite.config.js` to add admin-specific entry point:
  ```js
  export default defineConfig({
      plugins: [
          laravel({
              input: [
                  'resources/css/app.css',
                  'resources/js/app.js',
                  'resources/js/admin-echo.js',  // New: admin WebSocket client
              ],
              refresh: true,
          }),
      ],
  });
  ```
- [ ] Create `resources/js/admin-echo.js`:
  ```js
  import Echo from 'laravel-echo';
  import Pusher from 'pusher-js';
  window.Pusher = Pusher;

  window.Echo = new Echo({
      broadcaster: 'reverb',
      key: import.meta.env.VITE_REVERB_APP_KEY,
      wsHost: import.meta.env.VITE_REVERB_HOST,
      wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
      wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
      forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
      enabledTransports: ['ws', 'wss'],
  });

  // Make Echo available globally for inline scripts
  window.Echo = window.Echo;
  ```
- [ ] Add `@vite('resources/js/admin-echo.js')` to admin template layout (`resources/views/etiket/admin/template/index.blade.php`) before `@yield('js')`

**Option B: Use CDN/compiled bundle (Simpler but less maintainable)**

- [ ] Build Echo as standalone bundle and place in `public/js/echo.js`
- [ ] Add `<script src="{{ asset('js/echo.js') }}"></script>` to admin template
- [ ] This approach avoids changing the existing asset loading pattern

**Decision: Go with Option A** — the project already has Vite configured, just not used in admin views yet.

### 2.4 Add CSRF Token Meta Tag (Required for Channel Auth)

- [ ] Verify admin template `<head>` has: `<meta name="csrf-token" content="{{ csrf_token() }}">`
- [ ] If missing, add it to `resources/views/etiket/admin/template/index.blade.php`
- [ ] Axios already sends `X-Requested-With` header (confirmed in bootstrap.js)

### 2.5 Create Reusable Blade Components for Real-Time

- [ ] Create `resources/views/components/echo-listener.blade.php` (generic channel listener)
- [ ] Create `resources/js/echo-helpers.js` — utility functions:
  ```js
  // Helper to subscribe to emergency channel
  export function listenEmergency(destinasiId, callback) {
      return window.Echo.private(`emergency.${destinasiId}`)
          .listen('EmergencyTriggered', callback);
  }

  // Helper to subscribe to SOS channel
  export function listenSOS(destinasiId, callback) {
      return window.Echo.private(`sos.${destinasiId}`)
          .listen('SOSTriggered', callback);
  }

  // Helper for SOS chat
  export function listenSOSChat(sosId, callback) {
      return window.Echo.private(`sos-chat.${sosId}`)
          .listen('SOSMessageSent', callback);
  }
  ```

### 2.6 Connection Status Indicator

- [ ] Create a small UI component showing WebSocket connection status in admin navbar
- [ ] States: Connected (green dot), Connecting (yellow), Disconnected (red)
- [ ] Auto-reconnect logic (Echo handles this, but show status to user)
- [ ] Implementation:
  ```js
  window.Echo.connector.pusher.connection.bind('connected', () => {
      document.getElementById('ws-status').className = 'bg-success';
  });
  window.Echo.connector.pusher.connection.bind('disconnected', () => {
      document.getElementById('ws-status').className = 'bg-danger';
  });
  ```

---

## 5. Phase 3: Mobile Client Setup (API/Sanctum)

### 3.1 Broadcasting Auth for Sanctum Tokens

By default, `/broadcasting/auth` uses the `web` middleware (session-based). Mobile apps use Sanctum tokens. We need to support both.

- [ ] Create custom broadcasting auth route for API:
  ```php
  // routes/api.php
  Broadcast::routes(['middleware' => ['auth:sanctum']]);
  ```
- [ ] Alternatively, add `auth:sanctum` guard to channel authorization:
  ```php
  // routes/channels.php
  Broadcast::channel('emergency.{destinasiId}', function ($user, $destinasiId) {
      // Auth logic
  }, ['guards' => ['web', 'sanctum']]);
  ```
- [ ] Test that mobile app can authenticate to private channels using Bearer token

### 3.2 Mobile WebSocket Client Options

**For Flutter:**
- [ ] Document: Use `pusher_channels_flutter` package
- [ ] Configure with Reverb host/port (same as web, uses Pusher protocol)
- [ ] Auth endpoint: `POST /api/broadcasting/auth` with Bearer token

**For React Native:**
- [ ] Document: Use `@pusher/pusher-websocket-react-native` or `laravel-echo` + `pusher-js`
- [ ] Same configuration as web client but with token-based auth

### 3.3 Mobile Auth Header Configuration

- [ ] Mobile client must send Sanctum token in WebSocket auth request:
  ```js
  // React Native example
  window.Echo = new Echo({
      broadcaster: 'reverb',
      key: REVERB_APP_KEY,
      wsHost: 'yourdomain.com',
      wsPort: 443,
      wssPort: 443,
      forceTLS: true,
      enabledTransports: ['ws', 'wss'],
      authEndpoint: 'https://yourdomain.com/api/broadcasting/auth',
      auth: {
          headers: {
              Authorization: `Bearer ${sanctumToken}`,
              Accept: 'application/json',
          },
      },
  });
  ```

### 3.4 Offline Handling for Mobile

- [ ] Document reconnection strategy when mobile regains connectivity
- [ ] Echo auto-reconnects, but missed events during offline period are lost
- [ ] Solution: Mobile app calls `GET /api/emergency/active` on reconnect to sync state
- [ ] Solution: Mobile app calls `GET /api/sos/chat/{id}/messages?since={timestamp}` to catch up

---

## 6. Phase 4: Event & Channel Design

### 4.1 Create Event Classes

- [ ] Create `app/Events/EmergencyTriggered.php`
  ```php
  <?php
  namespace App\Events;

  use Illuminate\Broadcasting\InteractsWithSockets;
  use Illuminate\Broadcasting\PrivateChannel;
  use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
  use Illuminate\Queue\SerializesModels;

  class EmergencyTriggered implements ShouldBroadcast
  {
      use InteractsWithSockets, SerializesModels;

      public function __construct(
          public int $destinasiId,
          public string $title,
          public string $description,
          public string $severity,
          public ?float $latitude,
          public ?float $longitude,
          public ?string $hikerName,
          public int $emergencyId,
      ) {}

      public function broadcastOn(): array
      {
          return [
              new PrivateChannel('emergency.' . $this->destinasiId),
          ];
      }

      public function broadcastAs(): string
      {
          return 'emergency.triggered';
      }

      public function broadcastWith(): array
      {
          return [
              'id' => $this->emergencyId,
              'title' => $this->title,
              'description' => $this->description,
              'severity' => $this->severity,
              'latitude' => $this->latitude,
              'longitude' => $this->longitude,
              'hiker_name' => $this->hikerName,
              'timestamp' => now()->toISOString(),
          ];
      }
  }
  ```

- [ ] Create `app/Events/EmergencyBroadcast.php` (admin broadcasts to hikers)
- [ ] Create `app/Events/SOSTriggered.php`
- [ ] Create `app/Events/SOSMessageSent.php`
- [ ] Create `app/Events/SOSStatusUpdated.php`
- [ ] Create `app/Events/TrackingUpdated.php` (hiker position update for admin map)
- [ ] Create `app/Events/DisasterReported.php`

### 4.2 Define Channel Authorization

- [ ] Create `routes/channels.php` with all channel auth rules:
  ```php
  <?php
  use Illuminate\Support\Facades\Broadcast;
  use App\Models\destinasi;
  use App\Models\DestinasiUser;
  use App\Models\GkSos;

  // Emergency channel: admin assigned to destinasi OR active hiker at destinasi
  Broadcast::channel('emergency.{destinasiId}', function ($user, $destinasiId) {
      // Admin: check destinasi_user assignment
      if ($user->role === 'admin') {
          return DestinasiUser::where('user_id', $user->id)
              ->where('destinasi_id', $destinasiId)
              ->exists();
      }
      // Hiker: check active booking at this destinasi
      return $user->hasActiveBookingAt($destinasiId);
  }, ['guards' => ['web', 'sanctum']]);

  // SOS admin channel: only admins assigned to destinasi
  Broadcast::channel('sos.{destinasiId}', function ($user, $destinasiId) {
      return $user->role === 'admin' &&
          DestinasiUser::where('user_id', $user->id)
              ->where('destinasi_id', $destinasiId)
              ->exists();
  }, ['guards' => ['web', 'sanctum']]);

  // SOS Chat: only the hiker who triggered OR assigned admin
  Broadcast::channel('sos-chat.{sosId}', function ($user, $sosId) {
      $sos = GkSos::find($sosId);
      if (!$sos) return false;

      // Admin assigned to the destinasi
      if ($user->role === 'admin') {
          return DestinasiUser::where('user_id', $user->id)
              ->where('destinasi_id', $sos->id_destinasi)
              ->exists();
      }
      // The hiker who triggered the SOS
      return $sos->pendaki->booking->id_user === $user->id;
  }, ['guards' => ['web', 'sanctum']]);

  // Tracking channel: admin only
  Broadcast::channel('tracking.{destinasiId}', function ($user, $destinasiId) {
      return $user->role === 'admin' &&
          DestinasiUser::where('user_id', $user->id)
              ->where('destinasi_id', $destinasiId)
              ->exists();
  }, ['guards' => ['web', 'sanctum']]);
  ```

### 4.3 Add Helper Method to User Model

- [ ] Add `hasActiveBookingAt($destinasiId)` method to `User` model:
  ```php
  public function hasActiveBookingAt(int $destinasiId): bool
  {
      return $this->bookings()
          ->whereIn('status_booking', [6]) // checked-in
          ->whereHas('tiket.paketTiket', function ($q) use ($destinasiId) {
              $q->where('id_destinasi', $destinasiId);
          })
          ->exists();
  }
  ```

---

## 7. Phase 5: Queue Worker & Process Management

### 5.1 Verify Database Queue Tables

- [ ] Check if `jobs` table exists: `php artisan migrate:status`
- [ ] If missing, create: `php artisan queue:table && php artisan migrate`
- [ ] Verify `failed_jobs` table exists
- [ ] Verify `job_batches` table exists (for batch processing if needed)

### 5.2 Test Queue Processing Locally

- [ ] Start queue worker: `php artisan queue:work --queue=default`
- [ ] Dispatch a test event and verify it's processed
- [ ] Check `jobs` table is being consumed
- [ ] Test failed job handling: `php artisan queue:failed`

### 5.3 Configure Broadcast Queue

- [ ] Optionally create a dedicated `broadcast` queue for priority:
  ```php
  // In Event class
  public $queue = 'broadcast';
  ```
- [ ] Run separate worker for broadcast queue: `php artisan queue:work --queue=broadcast,default`
- [ ] This ensures broadcast events are processed with priority over other jobs

---

## 8. Phase 6: VPS Deployment (Nginx + Supervisor)

### 6.1 Nginx WebSocket Proxy Configuration

- [ ] Add WebSocket proxy to existing Nginx server block:
  ```nginx
  # /etc/nginx/sites-available/gunung-kerinci.conf
  # Add INSIDE the existing server { } block

  # WebSocket proxy for Laravel Reverb
  location /app {
      proxy_http_version 1.1;
      proxy_set_header Host $http_host;
      proxy_set_header Scheme $scheme;
      proxy_set_header SERVER_PORT $server_port;
      proxy_set_header REMOTE_ADDR $remote_addr;
      proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
      proxy_set_header Upgrade $http_upgrade;
      proxy_set_header Connection "upgrade";
      proxy_pass http://127.0.0.1:8080;
      proxy_read_timeout 60s;
      proxy_send_timeout 60s;
  }

  # Reverb apps endpoint
  location /apps {
      proxy_http_version 1.1;
      proxy_set_header Host $http_host;
      proxy_set_header Scheme $scheme;
      proxy_set_header SERVER_PORT $server_port;
      proxy_set_header REMOTE_ADDR $remote_addr;
      proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
      proxy_set_header Upgrade $http_upgrade;
      proxy_set_header Connection "upgrade";
      proxy_pass http://127.0.0.1:8080;
      proxy_read_timeout 60s;
      proxy_send_timeout 60s;
  }
  ```
- [ ] Test Nginx config: `sudo nginx -t`
- [ ] Reload Nginx: `sudo systemctl reload nginx`
- [ ] Verify WebSocket accessible via `wss://yourdomain.com/app/your-app-key`

### 6.2 Production Environment Variables

- [ ] Update production `.env`:
  ```env
  BROADCAST_CONNECTION=reverb

  REVERB_APP_ID=your-unique-app-id
  REVERB_APP_KEY=your-random-key-here
  REVERB_APP_SECRET=your-random-secret-here
  REVERB_HOST="yourdomain.com"
  REVERB_PORT=443
  REVERB_SCHEME=https

  VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
  VITE_REVERB_HOST="${REVERB_HOST}"
  VITE_REVERB_PORT="${REVERB_PORT}"
  VITE_REVERB_SCHEME="${REVERB_SCHEME}"
  ```
- [ ] Note: `REVERB_PORT=443` because Nginx proxies WSS (443) → Reverb (8080)
- [ ] Generate secure random strings for APP_KEY and APP_SECRET:
  ```bash
  php -r "echo bin2hex(random_bytes(16));" # for APP_KEY
  php -r "echo bin2hex(random_bytes(32));" # for APP_SECRET
  ```

### 6.3 Supervisor Configuration

- [ ] Install Supervisor (if not installed): `sudo apt install supervisor`
- [ ] Create Reverb process config:
  ```ini
  # /etc/supervisor/conf.d/reverb.conf
  [program:reverb]
  command=php /path/to/web-etiket-gunung-kerinci/artisan reverb:start --host=127.0.0.1 --port=8080
  autostart=true
  autorestart=true
  stopasgroup=true
  killasgroup=true
  user=www-data
  numprocs=1
  redirect_stderr=true
  stdout_logfile=/var/log/supervisor/reverb.log
  stdout_logfile_maxbytes=10MB
  stdout_logfile_backups=5
  stopwaitsecs=3600
  ```
- [ ] Create Queue Worker process config:
  ```ini
  # /etc/supervisor/conf.d/queue-worker.conf
  [program:queue-worker]
  process_name=%(program_name)s_%(process_num)02d
  command=php /path/to/web-etiket-gunung-kerinci/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
  autostart=true
  autorestart=true
  stopasgroup=true
  killasgroup=true
  user=www-data
  numprocs=2
  redirect_stderr=true
  stdout_logfile=/var/log/supervisor/queue-worker.log
  stdout_logfile_maxbytes=10MB
  stdout_logfile_backups=5
  stopwaitsecs=3600
  ```
- [ ] Load new configs: `sudo supervisorctl reread && sudo supervisorctl update`
- [ ] Start processes: `sudo supervisorctl start reverb` and `sudo supervisorctl start queue-worker:*`
- [ ] Verify running: `sudo supervisorctl status`

### 6.4 Deployment Script Updates

- [ ] After each deploy, restart queue workers: `sudo supervisorctl restart queue-worker:*`
- [ ] After config changes, restart Reverb: `sudo supervisorctl restart reverb`
- [ ] Add to deploy script:
  ```bash
  php artisan config:cache
  php artisan route:cache
  php artisan event:cache
  php artisan queue:restart  # Gracefully restart workers
  # Note: Do NOT restart Reverb on every deploy (drops connections)
  # Only restart Reverb if broadcasting config changes
  ```

### 6.5 SSL/TLS for WebSocket (WSS)

- [ ] Nginx handles SSL termination (existing Let's Encrypt cert covers this)
- [ ] WebSocket traffic goes through same port 443 as HTTPS
- [ ] No additional SSL cert needed — Nginx proxies `wss://` → `ws://127.0.0.1:8080`
- [ ] Verify: client connects to `wss://yourdomain.com/app/{key}` (encrypted)
- [ ] Reverb itself runs on plain `ws://` internally (safe, localhost only)

---

## 9. Phase 7: Testing & Verification

### 7.1 Local Development Testing

- [ ] Start Reverb: `php artisan reverb:start --debug`
- [ ] Start Queue: `php artisan queue:work`
- [ ] Start Vite: `npm run dev`
- [ ] Open admin panel in browser
- [ ] Check browser DevTools → Network → WS tab → verify WebSocket connection established
- [ ] Verify Echo connected: `console.log(window.Echo.connector.pusher.connection.state)` → "connected"

### 7.2 Event Broadcasting Test

- [ ] Create test command: `php artisan make:command TestBroadcast`
  ```php
  // app/Console/Commands/TestBroadcast.php
  public function handle()
  {
      broadcast(new \App\Events\EmergencyTriggered(
          destinasiId: 1,
          title: 'Test Emergency',
          description: 'This is a test broadcast',
          severity: 'low',
          latitude: -1.6955,
          longitude: 101.2637,
          hikerName: 'Test Hiker',
          emergencyId: 999,
      ));
      $this->info('Event broadcasted!');
  }
  ```
- [ ] Run: `php artisan test:broadcast`
- [ ] Verify event appears in browser console (via Echo listener)
- [ ] Verify event appears in Reverb debug output

### 7.3 Channel Authorization Test

- [ ] Test private channel auth as admin (should succeed)
- [ ] Test private channel auth as unauthorized user (should fail with 403)
- [ ] Test Sanctum token auth for mobile (should succeed with valid token)
- [ ] Test expired/invalid token (should fail gracefully)

### 7.4 Production Verification

- [ ] After deploy, check `sudo supervisorctl status` — all processes RUNNING
- [ ] Check Reverb log: `tail -f /var/log/supervisor/reverb.log`
- [ ] Check Queue log: `tail -f /var/log/supervisor/queue-worker.log`
- [ ] Open admin panel on production → verify WebSocket connects (green status dot)
- [ ] Run test broadcast on production → verify received in browser
- [ ] Test from mobile app → verify WebSocket connects with Sanctum token

### 7.5 Load & Stress Testing

- [ ] Test with 10 concurrent WebSocket connections
- [ ] Test with 50 concurrent connections (expected peak for Gunung Kerinci)
- [ ] Monitor VPS memory usage during connections (`htop`)
- [ ] Monitor Reverb process memory: should stay under 50MB for <100 connections
- [ ] Test rapid event broadcasting (10 events/second) — verify no message loss

---

## 10. Channel Authorization Map

```
┌─────────────────────────────────────────────────────────────────────┐
│                    CHANNEL AUTHORIZATION MATRIX                       │
├─────────────────────────┬──────────┬──────────┬─────────────────────┤
│ Channel                 │ Admin    │ Hiker    │ Condition            │
│                         │ (web)    │ (mobile) │                      │
├─────────────────────────┼──────────┼──────────┼─────────────────────┤
│ private-emergency.{did} │ ✅       │ ✅       │ Admin: assigned to   │
│                         │          │          │ destinasi            │
│                         │          │          │ Hiker: active booking│
│                         │          │          │ status=6 (checked-in)│
├─────────────────────────┼──────────┼──────────┼─────────────────────┤
│ private-sos.{did}       │ ✅       │ ❌       │ Admin assigned to    │
│                         │          │          │ destinasi only       │
├─────────────────────────┼──────────┼──────────┼─────────────────────┤
│ private-sos-chat.{sid}  │ ✅       │ ✅       │ Admin: assigned to   │
│                         │          │          │ SOS's destinasi      │
│                         │          │          │ Hiker: owns the SOS  │
├─────────────────────────┼──────────┼──────────┼─────────────────────┤
│ private-tracking.{did}  │ ✅       │ ❌       │ Admin assigned to    │
│                         │          │          │ destinasi only       │
├─────────────────────────┼──────────┼──────────┼─────────────────────┤
│ presence-monitoring.{d} │ ✅       │ ❌       │ Admin assigned to    │
│                         │          │          │ destinasi only       │
├─────────────────────────┼──────────┼──────────┼─────────────────────┤
│ public-announcement     │ ✅       │ ✅       │ No auth required     │
└─────────────────────────┴──────────┴──────────┴─────────────────────┘

Legend: {did} = destinasi_id, {sid} = sos_id, {d} = destinasi_id
```

---

## 11. Event Catalog

| Event Class | Channel | Trigger | Payload |
|-------------|---------|---------|---------|
| `EmergencyTriggered` | `private-emergency.{did}` | Hiker presses emergency button | id, title, description, severity, lat, lng, hiker_name, timestamp |
| `EmergencyBroadcast` | `private-emergency.{did}` | Admin broadcasts alert | id, title, description, severity, admin_name, timestamp |
| `EmergencyResolved` | `private-emergency.{did}` | Admin resolves emergency | id, resolved_by, resolution_notes, timestamp |
| `SOSTriggered` | `private-sos.{did}` | Hiker presses SOS panic button | id, severity, lat, lng, hiker_name, hiker_phone, emergency_contact, booking_id, timestamp |
| `SOSStatusUpdated` | `private-sos.{did}` + `private-sos-chat.{sid}` | Admin changes SOS status | id, old_status, new_status, admin_name, timestamp |
| `SOSMessageSent` | `private-sos-chat.{sid}` | Either party sends message | id, sender_type, sender_name, type (text/image), content, timestamp |
| `TrackingUpdated` | `private-tracking.{did}` | Hiker GPS position received | pendaki_id, lat, lng, altitude, accuracy, hiker_name, timestamp |
| `DisasterReported` | `private-sos.{did}` | Hiker submits disaster report | id, potensi_bencana, lokasi, hiker_name, timestamp |

---

## 12. Environment Variables Reference

### Development (`.env`)

```env
# Broadcasting
BROADCAST_CONNECTION=reverb

# Reverb Server
REVERB_APP_ID=local-dev-app
REVERB_APP_KEY=local-dev-key
REVERB_APP_SECRET=local-dev-secret
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http

# Vite (exposed to frontend)
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"

# Queue (already exists)
QUEUE_CONNECTION=database
```

### Production (`.env`)

```env
# Broadcasting
BROADCAST_CONNECTION=reverb

# Reverb Server (behind Nginx proxy)
REVERB_APP_ID=gk-prod-xxxxxxxx
REVERB_APP_KEY=gk-prod-key-xxxxxxxxxxxxxxxx
REVERB_APP_SECRET=gk-prod-secret-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
REVERB_HOST="yourdomain.com"
REVERB_PORT=443
REVERB_SCHEME=https

# Internal Reverb binding (Supervisor uses this)
REVERB_SERVER_HOST=127.0.0.1
REVERB_SERVER_PORT=8080

# Vite (exposed to frontend)
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"

# Queue
QUEUE_CONNECTION=database
```

---

## 13. Troubleshooting Guide

### Common Issues

| Problem | Cause | Solution |
|---------|-------|----------|
| WebSocket connection refused | Reverb not running | Check `supervisorctl status reverb` |
| 403 on channel subscription | Auth failed | Check `routes/channels.php` logic, verify user role/assignment |
| Events not received | Queue worker not running | Check `supervisorctl status queue-worker` |
| Events dispatched but not broadcast | `BROADCAST_CONNECTION=log` | Change to `reverb` in `.env`, run `php artisan config:cache` |
| WSS connection fails in production | Nginx not proxying | Check `/app` location block in Nginx config |
| Mobile can't auth to private channel | Sanctum guard not configured | Add `'guards' => ['web', 'sanctum']` to channel definition |
| "Class not found" for Event | Event not cached | Run `php artisan event:cache` or `event:clear` |
| Messages delayed | Queue backlog | Check `jobs` table count, increase `numprocs` in Supervisor |
| Reverb crashes under load | Memory limit | Increase PHP memory limit for Reverb process, or add `--memory=256` flag |

### Debugging Commands

```bash
# Check Reverb is running
sudo supervisorctl status reverb

# Watch Reverb logs live
tail -f /var/log/supervisor/reverb.log

# Start Reverb in debug mode (shows all connections/events)
php artisan reverb:start --debug

# Check pending broadcast jobs
php artisan tinker --execute="echo DB::table('jobs')->count();"

# Test broadcasting manually
php artisan tinker --execute="broadcast(new App\Events\EmergencyTriggered(...));"

# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Monitor queue in real-time
php artisan queue:monitor database:default
```

### Health Check Endpoint (Optional)

- [ ] Create `GET /api/health/websocket` endpoint that verifies:
  - Reverb process is running (check port 8080 is open)
  - Queue worker is processing (dispatch test job, verify completion)
  - Return status for monitoring/alerting

---

## Implementation Order (Step-by-Step)

```
Week 1: Server Setup
├── Day 1: Install Reverb + configure env (Phase 1)
├── Day 2: Client-side Echo setup + admin template integration (Phase 2)
└── Day 3: Create first Event class + test locally (Phase 4 partial)

Week 2: Channels & Auth
├── Day 4: Define all channels + authorization logic (Phase 4)
├── Day 5: Mobile client auth setup + Sanctum integration (Phase 3)
└── Day 6: Create all Event classes (Phase 4)

Week 3: Deployment
├── Day 7: Supervisor + Nginx config on VPS (Phase 6)
├── Day 8: Production testing + SSL verification (Phase 7)
└── Day 9: Load testing + monitoring setup (Phase 7)
```

**Total: ~9 working days** to have WebSocket fully operational and ready for Pesan Darurat & SOS features.

---

## Dependencies for Next Plans

Once this WebSocket plan is complete, the following features can be built on top:

| Feature | Depends on Events | Depends on Channels |
|---------|------------------|---------------------|
| Pesan Darurat | `EmergencyTriggered`, `EmergencyBroadcast` | `private-emergency.{did}` |
| SOS Panic Button | `SOSTriggered` | `private-sos.{did}` |
| SOS Chat | `SOSMessageSent` | `private-sos-chat.{sid}` |
| Live Tracking Map | `TrackingUpdated` | `private-tracking.{did}` |
| Disaster Reports | `DisasterReported` | `private-sos.{did}` |
