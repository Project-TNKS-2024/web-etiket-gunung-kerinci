# Implementation Plan: Pesan Darurat, Pelacakan Jejak & SOS

> **Project**: Web E-Tiket Gunung Kerinci (TNKS)  
> **Date**: 2026-05-05  
> **Status**: Planning Phase  
> **Stack**: Laravel 11 + Blade + Sanctum API + MySQL + Vite

---

## Table of Contents

1. [Problem Analysis & Deep Questions](#1-problem-analysis--deep-questions)
2. [Architecture Overview](#2-architecture-overview)
3. [Phase 1: Foundation & Infrastructure](#3-phase-1-foundation--infrastructure)
4. [Phase 2: Pesan Darurat (Emergency Message)](#4-phase-2-pesan-darurat-emergency-message)
5. [Phase 3: Pelacakan Jejak (Trail Tracking)](#5-phase-3-pelacakan-jejak-trail-tracking)
6. [Phase 4: SOS System](#6-phase-4-sos-system)
7. [Phase 5: Admin Dashboard & Monitoring](#7-phase-5-admin-dashboard--monitoring)
8. [Phase 6: Testing & Deployment](#8-phase-6-testing--deployment)
9. [Database Schema Design](#9-database-schema-design)
10. [API Endpoint Design](#10-api-endpoint-design)
11. [Technical Decisions & Trade-offs](#11-technical-decisions--trade-offs)

---

## 1. Problem Analysis & Deep Questions

### Problem Statement

Hikers on Gunung Kerinci need safety features beyond ticketing:
- **Pesan Darurat**: Emergency notifications with GPS location from mobile
- **Pelacakan Jejak**: Trail checkpoint tracking (QR scan + GPS-based proximity)
- **SOS**: Panic button, messaging, calls, disaster reporting

### Deep Questions & Answers

#### Pesan Darurat (Emergency Message)

| # | Question | Analysis/Answer |
|---|----------|-----------------|
| 1 | Who triggers the emergency message — the hiker or the admin? | **Both**: Hiker uploads GPS + triggers alert from mobile. Admin can also broadcast emergency (e.g., volcanic activity, weather). |
| 2 | What "upload GPS from mobile" means technically? | The mobile app sends `{latitude, longitude, altitude, accuracy, timestamp}` to the API. This is the hiker's last known position. |
| 3 | Who receives the emergency notification? | **Admin panel** (real-time dashboard), **other hikers** on the same route (push/in-app), and **rescue team** (if integrated). |
| 4 | Should the system store GPS history or just the last position? | **Both**: Store periodic GPS updates (trail tracking) AND highlight the emergency-triggered position separately. |
| 5 | What happens if the hiker has no internet when emergency occurs? | Mobile app should **queue** the emergency locally and send when connectivity resumes. Also consider SMS fallback. |
| 6 | Is "lokasi terakhir yang direcord system" the last GPS ping or the last checkpoint? | **Last GPS ping** from periodic tracking. If GPS tracking fails, fall back to last checkpoint (QR scan). |

#### Pelacakan Jejak (Trail Tracking)

| # | Question | Analysis/Answer |
|---|----------|-----------------|
| 1 | What are "posts" in the context of radius calculation? | Physical checkpoints (pos) along the trail — e.g., Pos 1 (2400m), Pos 2 (2800m), Pos 3 (3200m). Each has a known GPS coordinate. |
| 2 | How many posts exist on Gunung Kerinci trail? | Typically 3-7 posts per route. Need a `gk_posts` table with coordinates per gate/route. |
| 3 | What radius threshold determines "near a post"? | Suggest **100-200 meters** (configurable). Mountain GPS accuracy is typically 5-15m in open areas, worse in forest canopy. |
| 4 | Can QR scan and GPS proximity coexist? | **Yes** — QR is the primary (reliable) method. GPS proximity is the **fallback/alternative** when QR is damaged or unreadable. |
| 5 | What does "absen" (attendance) mean here? | Confirming the hiker has reached a specific post. Creates a timestamped record: `{hiker, post, timestamp, method: qr|gps|manual}`. |
| 6 | Should tracking be per-individual or per-booking-group? | **Per-individual** (`gk_pendaki`). Each hiker in a group may reach posts at different times. |
| 7 | What if GPS is inaccurate in dense forest? | Allow **manual button press** as third option (with GPS coordinate attached even if inaccurate). Admin can verify later. |

#### SOS System

| # | Question | Analysis/Answer |
|---|----------|-----------------|
| 1 | What's the difference between "Pesan Darurat" and "SOS"? | **Pesan Darurat** = system-level emergency broadcast (admin-to-hikers or hiker-to-admin alert). **SOS** = personal panic/help request with communication channel. |
| 2 | WebSocket for messaging — is this feasible given current stack? | Current project has **zero WebSocket infrastructure**. Options: (a) Laravel Reverb (native L11), (b) Pusher/Ably, (c) polling fallback. Recommend **Laravel Reverb** for self-hosted or **Pusher** for managed. |
| 3 | "Kirim text dan gambar" — is this a full chat or one-way? | **Two-way chat** between hiker and admin/rescue team. Supports text + image attachments. |
| 4 | WhatsApp call redirect — how to implement? | Simple `wa.me/{phone}` deep link. The app opens WhatsApp with pre-filled rescue team number. No API integration needed. |
| 5 | "Lapor Potensi Bencana" — who can report? | **Any authenticated hiker** currently on the mountain (status = checked-in). Reports go to admin for verification before broadcast. |
| 6 | SOS signal — what exactly happens when pressed? | (a) Record GPS, (b) Create SOS record with status "active", (c) Send real-time notification to admin, (d) Optionally trigger SMS to emergency contact (`no_hp_darurat` in biodata). |
| 7 | Should SOS have severity levels? | **Yes**: `low` (need assistance), `medium` (injured/lost), `high` (life-threatening). Affects admin response priority. |
| 8 | What about false SOS triggers? | Add confirmation step ("Are you sure?") + cooldown period. Admin can mark as false alarm. |

---

## 2. Architecture Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                        MOBILE APP (Flutter/RN)                    │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────────────┐│
│  │GPS Upload│  │QR Scanner│  │SOS Button│  │Chat/Messaging    ││
│  └────┬─────┘  └────┬─────┘  └────┬─────┘  └────────┬─────────┘│
└───────┼──────────────┼──────────────┼────────────────┼──────────┘
        │              │              │                │
        ▼              ▼              ▼                ▼
┌─────────────────────────────────────────────────────────────────┐
│                     LARAVEL API (Sanctum)                         │
│  ┌──────────────┐  ┌────────────┐  ┌───────────┐  ┌──────────┐ │
│  │GPS Tracking  │  │Checkpoint  │  │SOS/Emergency│ │WebSocket │ │
│  │Controller    │  │Controller  │  │Controller   │ │(Reverb)  │ │
│  └──────┬───────┘  └─────┬──────┘  └──────┬──────┘ └────┬─────┘ │
│         │                │               │              │        │
│  ┌──────▼───────────────▼───────────────▼──────────────▼──────┐ │
│  │                    SERVICE LAYER                             │ │
│  │  EmergencyService | TrackingService | SOSService | ChatSvc  │ │
│  └──────────────────────────┬──────────────────────────────────┘ │
│                             │                                    │
│  ┌──────────────────────────▼──────────────────────────────────┐ │
│  │                      DATABASE (MySQL)                        │ │
│  │  gk_posts | gk_tracking | gk_emergency | gk_sos | gk_chat  │ │
│  └─────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────┘
        │
        ▼
┌─────────────────────────────────────────────────────────────────┐
│                     ADMIN WEB PANEL (Blade)                       │
│  ┌──────────────┐  ┌────────────┐  ┌───────────────────────────┐│
│  │Live Map      │  │SOS Alerts  │  │Emergency Broadcast Panel  ││
│  │(Leaflet.js)  │  │Dashboard   │  │Chat Interface             ││
│  └──────────────┘  └────────────┘  └───────────────────────────┘│
└─────────────────────────────────────────────────────────────────┘
```

---

## 3. Phase 1: Foundation & Infrastructure

### 1.1 WebSocket Setup (Laravel Reverb)

- [ ] Install Laravel Reverb (`php artisan install:broadcasting`)
- [ ] Configure Reverb in `.env` (host, port, app credentials)
- [ ] Install Laravel Echo + Pusher JS client on frontend (`npm install`)
- [ ] Create base broadcasting channel authorization (`routes/channels.php`)
- [ ] Test basic WebSocket connection (admin panel)
- [ ] Configure Reverb for production (Supervisor process, Nginx proxy)

### 1.2 GPS & Map Infrastructure

- [ ] Install Leaflet.js via npm for admin map views
- [ ] Create reusable Blade component for map display (`components/map.blade.php`)
- [ ] Define GPS coordinate validation rules (latitude: -90 to 90, longitude: -180 to 180)
- [ ] Create `GeoHelper` utility class (distance calculation using Haversine formula)
- [ ] Seed Gunung Kerinci trail post coordinates into database

### 1.3 Database Migrations (New Tables)

- [ ] Create migration: `gk_posts` (trail checkpoints with GPS coordinates)
- [ ] Create migration: `gk_tracking` (GPS location history per hiker)
- [ ] Create migration: `gk_checkpoint_logs` (QR/GPS/manual check-in at posts)
- [ ] Create migration: `gk_emergency_messages` (emergency broadcasts)
- [ ] Create migration: `gk_sos` (SOS panic button records)
- [ ] Create migration: `gk_sos_chats` (SOS chat messages)
- [ ] Create migration: `gk_disaster_reports` (disaster potential reports)
- [ ] Run migrations and verify schema

### 1.4 Model & Relationship Setup

- [ ] Create Model: `GkPost` (belongs to gate/destinasi)
- [ ] Create Model: `GkTracking` (belongs to gk_pendaki)
- [ ] Create Model: `GkCheckpointLog` (belongs to gk_pendaki, gk_post)
- [ ] Create Model: `GkEmergencyMessage` (belongs to user/admin)
- [ ] Create Model: `GkSos` (belongs to gk_pendaki)
- [ ] Create Model: `GkSosChat` (belongs to gk_sos)
- [ ] Create Model: `GkDisasterReport` (belongs to user)
- [ ] Define all relationships in existing models (`gk_pendaki`, `gk_gates`, `destinasi`)

### 1.5 Permission & Role Setup

- [ ] Add new permissions: `manage-emergency`, `view-tracking`, `respond-sos`, `manage-posts`
- [ ] Assign permissions to existing roles (Super Admin, Admin Destinasi)
- [ ] Create middleware for emergency feature access

---

## 4. Phase 2: Pesan Darurat (Emergency Message)

### 2.1 GPS Upload from Mobile

- [ ] Create API endpoint: `POST /api/tracking/gps` (upload current GPS position)
- [ ] Validate GPS payload: `{latitude, longitude, altitude?, accuracy?, battery_level?, timestamp}`
- [ ] Store in `gk_tracking` table linked to `gk_pendaki` (active booking)
- [ ] Implement rate limiting (max 1 GPS update per 30 seconds per user)
- [ ] Handle offline queue: accept batch GPS uploads `POST /api/tracking/gps/batch`
- [ ] Return acknowledgment with server timestamp

### 2.2 Emergency Notification System

- [ ] Create API endpoint: `POST /api/emergency/trigger` (hiker triggers emergency)
- [ ] Payload: `{title, description, latitude, longitude, severity: low|medium|high}`
- [ ] Auto-attach last known GPS from `gk_tracking` if coordinates not provided
- [ ] Create `EmergencyTriggered` event + broadcast on WebSocket channel
- [ ] Send notification to all admins assigned to the destination (`destinasi_user`)
- [ ] Store emergency record with status lifecycle: `active → acknowledged → resolved`

### 2.3 Admin Emergency Broadcast

- [ ] Create admin endpoint: `POST /admin/emergency/broadcast`
- [ ] Admin can broadcast to all hikers currently checked-in at a destination
- [ ] Payload: `{title, description, severity, destinasi_id}`
- [ ] Create `EmergencyBroadcast` event for mobile push channel
- [ ] Store broadcast record for audit trail
- [ ] Show broadcast history in admin panel

### 2.4 Emergency Dashboard (Admin Web)

- [ ] Create admin view: `etiket/admin/emergency/index.blade.php`
- [ ] Display active emergencies with map (Leaflet) showing hiker location
- [ ] Real-time updates via Laravel Echo (listen to emergency channel)
- [ ] Action buttons: Acknowledge, Assign Rescue, Resolve, Escalate
- [ ] Show hiker details (biodata, booking, emergency contact `no_hp_darurat`)
- [ ] Emergency history log with filters (date, severity, status)

### 2.5 Mobile API for Receiving Emergencies

- [ ] Create API endpoint: `GET /api/emergency/active` (get active emergencies for hiker's destination)
- [ ] WebSocket channel: `private-emergency.{destinasi_id}` for real-time push
- [ ] Include admin broadcasts and other hikers' emergencies (anonymized)

---

## 5. Phase 3: Pelacakan Jejak (Trail Tracking)

### 3.1 Post (Checkpoint) Management

- [ ] Create admin CRUD for posts: `etiket/admin/destinasi/posts/`
- [ ] Fields: nama, urutan (order), latitude, longitude, altitude, radius_meter, gate_id, qr_code_value
- [ ] Generate unique QR code value per post (UUID or encoded string)
- [ ] Display posts on map in admin panel (Leaflet markers)
- [ ] Allow admin to set post order along the trail
- [ ] Seed initial Gunung Kerinci posts (Shelter 1, Pos 1, Pos 2, Pos 3, Puncak)

### 3.2 QR Code Scanning (Primary Method)

- [ ] Create API endpoint: `POST /api/tracking/checkpoint/qr`
- [ ] Payload: `{qr_code_value, latitude?, longitude?}` (GPS optional but recommended)
- [ ] Validate QR code exists in `gk_posts`
- [ ] Validate hiker has active booking with status = checked-in (status 6)
- [ ] Prevent duplicate scan (same post within 1 hour)
- [ ] Record in `gk_checkpoint_logs`: `{pendaki_id, post_id, method: 'qr', timestamp, gps}`
- [ ] Return post info + next post info + progress percentage

### 3.3 GPS Proximity Detection (Alternative Method)

- [ ] Create API endpoint: `POST /api/tracking/checkpoint/gps`
- [ ] Payload: `{latitude, longitude, accuracy}`
- [ ] Calculate distance to ALL posts on the hiker's route using Haversine formula
- [ ] Find nearest post within configured radius (default 150m)
- [ ] If within radius AND accuracy < 50m: auto-suggest check-in
- [ ] Return: `{nearest_post, distance_meters, within_radius: bool, all_posts_with_distances}`
- [ ] Do NOT auto-record — require explicit confirmation (button press)

### 3.4 Manual Check-in Button (Frontend/Mobile)

- [ ] Create API endpoint: `POST /api/tracking/checkpoint/manual`
- [ ] Payload: `{post_id, latitude, longitude}` (user selects post + confirms)
- [ ] Validate proximity (warn if > 500m but still allow with flag `is_manual_override`)
- [ ] Record in `gk_checkpoint_logs`: `{method: 'manual', is_manual_override: bool}`
- [ ] Admin can review manual overrides for verification

### 3.5 Tracking Progress View

- [ ] Create API endpoint: `GET /api/tracking/progress/{booking_id}`
- [ ] Return: all posts on route, which are completed, timestamps, current position
- [ ] Create admin view: `etiket/admin/destinasi/tracking/index.blade.php`
- [ ] Show all active hikers on map with their last known position
- [ ] Show checkpoint progress per hiker (timeline view)
- [ ] Filter by booking, date, gate, status
- [ ] Real-time position updates on map via WebSocket

### 3.6 Trail Progress Notifications

- [ ] Notify admin when hiker reaches key checkpoints (summit, last post before descent)
- [ ] Notify admin if hiker hasn't checked in for X hours (configurable, e.g., 6 hours)
- [ ] Create "overdue hiker" alert system based on expected pace

---

## 6. Phase 4: SOS System

### 4.1 Panic Button (SOS Signal)

- [ ] Create API endpoint: `POST /api/sos/trigger`
- [ ] Payload: `{latitude, longitude, severity: low|medium|high, message?}`
- [ ] Require confirmation (mobile handles double-tap or hold gesture)
- [ ] Create SOS record with status: `active`
- [ ] Broadcast `SOSTriggered` event to admin WebSocket channel
- [ ] Auto-include: hiker biodata, booking info, last tracking data, emergency contact
- [ ] Send SMS to `no_hp_darurat` (emergency contact) via SMS gateway (optional Phase 2)
- [ ] Implement cooldown: max 1 SOS per 5 minutes (prevent spam)
- [ ] Return SOS ID + confirmation + rescue team contact info

### 4.2 SOS Chat (WebSocket Messaging)

- [ ] Create WebSocket channel: `private-sos-chat.{sos_id}`
- [ ] Create API endpoint: `POST /api/sos/chat/{sos_id}/send`
- [ ] Payload: `{type: 'text'|'image', content: string, image?: file}`
- [ ] Store messages in `gk_sos_chats` table
- [ ] Support image upload (store in `storage/app/public/sos/`)
- [ ] Create `SOSMessageSent` event for real-time broadcast
- [ ] Create API endpoint: `GET /api/sos/chat/{sos_id}/messages` (paginated history)
- [ ] Admin chat interface in web panel (Blade + Echo)
- [ ] Show typing indicators (optional, nice-to-have)
- [ ] Mark messages as read

### 4.3 WhatsApp Call Redirect

- [ ] Create API endpoint: `GET /api/sos/call-options`
- [ ] Return rescue team phone numbers configured in `settings` table
- [ ] Generate WhatsApp deep link: `https://wa.me/{phone}?text={pre-filled-message}`
- [ ] Pre-filled message includes: hiker name, location coordinates, booking ID
- [ ] Admin configures rescue team numbers in Settings panel
- [ ] Also provide direct phone call link (`tel:{number}`) as alternative

### 4.4 Lapor Potensi Bencana (Disaster Report)

- [ ] Create API endpoint: `POST /api/sos/disaster-report`
- [ ] Payload: `{potensi_bencana: string, deskripsi: text, lokasi: string, latitude?, longitude?, lampiran: image}`
- [ ] Validate user has active checked-in booking
- [ ] Store in `gk_disaster_reports` table
- [ ] Upload image attachment to `storage/app/public/disaster-reports/`
- [ ] Notify admins via WebSocket + email
- [ ] Admin review workflow: `pending → verified → broadcast → resolved`
- [ ] If verified, admin can escalate to emergency broadcast (Phase 2.3)

### 4.5 SOS Admin Response Panel

- [ ] Create admin view: `etiket/admin/sos/index.blade.php` (active SOS list)
- [ ] Create admin view: `etiket/admin/sos/detail.blade.php` (SOS detail + chat)
- [ ] Real-time SOS alert with sound notification in admin panel
- [ ] Show SOS on map with hiker location
- [ ] Action buttons: Acknowledge, Dispatch Rescue, Resolve, Mark False Alarm
- [ ] SOS status lifecycle: `active → acknowledged → dispatched → resolved | false_alarm`
- [ ] Show all disaster reports with verification workflow
- [ ] SOS history and statistics

---

## 7. Phase 5: Admin Dashboard & Monitoring

### 5.1 Live Monitoring Dashboard

- [ ] Create admin view: `etiket/admin/monitoring/index.blade.php`
- [ ] Full-screen map (Leaflet) showing all active hikers' positions
- [ ] Color-coded markers: green (normal), yellow (slow/overdue), red (SOS/emergency)
- [ ] Sidebar: list of active hikers with status, last update time
- [ ] Click hiker marker → show detail popup (biodata, progress, booking)
- [ ] Real-time updates via WebSocket (positions update every 30s-60s)

### 5.2 Alert & Notification Center

- [ ] Create notification bell in admin navbar
- [ ] Aggregate: SOS alerts, emergency messages, overdue hikers, disaster reports
- [ ] Priority-based ordering (SOS > Emergency > Overdue > Reports)
- [ ] Sound alerts for high-priority notifications
- [ ] Mark as read/dismissed functionality

### 5.3 Reports & Analytics

- [ ] Trail usage statistics (most/least visited posts)
- [ ] Average hiking time between posts
- [ ] SOS frequency analysis
- [ ] Emergency response time metrics
- [ ] Export reports (PDF/Excel)

---

## 8. Phase 6: Testing & Deployment

### 6.1 Unit & Feature Tests

- [ ] Test GPS upload endpoint (valid/invalid coordinates)
- [ ] Test checkpoint QR scan (valid code, duplicate prevention, unauthorized)
- [ ] Test GPS proximity calculation (Haversine accuracy)
- [ ] Test SOS trigger (cooldown, validation, broadcast)
- [ ] Test chat messaging (WebSocket events, image upload)
- [ ] Test emergency broadcast (permissions, delivery)
- [ ] Test disaster report workflow (submission, admin review)

### 6.2 Integration Testing

- [ ] Test full flow: GPS upload → tracking → checkpoint → SOS → chat → resolve
- [ ] Test WebSocket connections under load
- [ ] Test offline GPS batch upload
- [ ] Test concurrent SOS from multiple hikers

### 6.3 Deployment & DevOps

- [ ] Configure Reverb for production (Supervisor, Nginx WebSocket proxy)
- [ ] Set up file storage for SOS images (S3 or local with backup)
- [ ] Configure rate limiting in production
- [ ] Set up monitoring for WebSocket server health
- [ ] Create database backup strategy for tracking data (high volume)
- [ ] Document API endpoints in `docs/api.md` (extend existing)

---

## 9. Database Schema Design

### `gk_posts` (Trail Checkpoints)

```sql
CREATE TABLE gk_posts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,           -- "Pos 1", "Shelter 2", "Puncak"
    urutan INT NOT NULL,                   -- Order on trail (1, 2, 3...)
    latitude DECIMAL(10, 8) NOT NULL,      -- GPS latitude
    longitude DECIMAL(11, 8) NOT NULL,     -- GPS longitude
    altitude INT NULLABLE,                 -- Elevation in meters
    radius_meter INT DEFAULT 150,          -- Check-in radius threshold
    id_gate BIGINT UNSIGNED NOT NULL,      -- Which gate/route this post belongs to
    qr_code_value VARCHAR(255) UNIQUE,     -- Unique QR code identifier
    status BOOLEAN DEFAULT TRUE,           -- Active/inactive
    detail TEXT NULLABLE,                  -- Description
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (id_gate) REFERENCES gk_gates(id)
);
```

### `gk_tracking` (GPS Location History)

```sql
CREATE TABLE gk_tracking (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_pendaki CHAR(36) NOT NULL,          -- UUID from gk_pendakis
    id_booking CHAR(36) NOT NULL,          -- UUID from gk_bookings
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    altitude DECIMAL(7, 2) NULLABLE,       -- Meters
    accuracy DECIMAL(6, 2) NULLABLE,       -- GPS accuracy in meters
    battery_level TINYINT NULLABLE,        -- 0-100
    recorded_at TIMESTAMP NOT NULL,        -- When GPS was captured (mobile time)
    created_at TIMESTAMP,                  -- When server received it
    FOREIGN KEY (id_pendaki) REFERENCES gk_pendakis(id) ON DELETE CASCADE,
    FOREIGN KEY (id_booking) REFERENCES gk_bookings(id) ON DELETE CASCADE,
    INDEX idx_pendaki_time (id_pendaki, recorded_at),
    INDEX idx_booking (id_booking)
);
```

### `gk_checkpoint_logs` (Post Check-in Records)

```sql
CREATE TABLE gk_checkpoint_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_pendaki CHAR(36) NOT NULL,
    id_post BIGINT UNSIGNED NOT NULL,
    id_booking CHAR(36) NOT NULL,
    method ENUM('qr', 'gps', 'manual') NOT NULL,
    latitude DECIMAL(10, 8) NULLABLE,
    longitude DECIMAL(11, 8) NULLABLE,
    is_manual_override BOOLEAN DEFAULT FALSE,
    checked_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (id_pendaki) REFERENCES gk_pendakis(id) ON DELETE CASCADE,
    FOREIGN KEY (id_post) REFERENCES gk_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (id_booking) REFERENCES gk_bookings(id) ON DELETE CASCADE,
    UNIQUE KEY unique_checkin (id_pendaki, id_post, id_booking),
    INDEX idx_booking_progress (id_booking, checked_at)
);
```

### `gk_emergency_messages` (Emergency Broadcasts)

```sql
CREATE TABLE gk_emergency_messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_user BIGINT UNSIGNED NOT NULL,       -- Who triggered (hiker or admin)
    id_destinasi BIGINT UNSIGNED NOT NULL,
    id_pendaki CHAR(36) NULLABLE,           -- If triggered by hiker
    id_booking CHAR(36) NULLABLE,
    type ENUM('hiker_alert', 'admin_broadcast') NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    severity ENUM('low', 'medium', 'high', 'critical') NOT NULL,
    latitude DECIMAL(10, 8) NULLABLE,
    longitude DECIMAL(11, 8) NULLABLE,
    status ENUM('active', 'acknowledged', 'resolved') DEFAULT 'active',
    acknowledged_by BIGINT UNSIGNED NULLABLE,
    resolved_by BIGINT UNSIGNED NULLABLE,
    resolved_at TIMESTAMP NULLABLE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id),
    FOREIGN KEY (id_destinasi) REFERENCES destinasis(id),
    FOREIGN KEY (id_pendaki) REFERENCES gk_pendakis(id) ON DELETE SET NULL,
    FOREIGN KEY (id_booking) REFERENCES gk_bookings(id) ON DELETE SET NULL,
    INDEX idx_active (status, id_destinasi),
    INDEX idx_severity (severity, status)
);
```

### `gk_sos` (SOS Panic Button Records)

```sql
CREATE TABLE gk_sos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_pendaki CHAR(36) NOT NULL,
    id_booking CHAR(36) NOT NULL,
    id_destinasi BIGINT UNSIGNED NOT NULL,
    severity ENUM('low', 'medium', 'high') NOT NULL,
    message TEXT NULLABLE,                  -- Initial message
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    altitude DECIMAL(7, 2) NULLABLE,
    status ENUM('active', 'acknowledged', 'dispatched', 'resolved', 'false_alarm') DEFAULT 'active',
    acknowledged_by BIGINT UNSIGNED NULLABLE,
    resolved_by BIGINT UNSIGNED NULLABLE,
    resolved_at TIMESTAMP NULLABLE,
    resolution_notes TEXT NULLABLE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (id_pendaki) REFERENCES gk_pendakis(id) ON DELETE CASCADE,
    FOREIGN KEY (id_booking) REFERENCES gk_bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (id_destinasi) REFERENCES destinasis(id),
    INDEX idx_active_sos (status, id_destinasi),
    INDEX idx_pendaki (id_pendaki)
);
```

### `gk_sos_chats` (SOS Chat Messages)

```sql
CREATE TABLE gk_sos_chats (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_sos BIGINT UNSIGNED NOT NULL,
    sender_id BIGINT UNSIGNED NOT NULL,     -- User ID (hiker or admin)
    sender_type ENUM('hiker', 'admin') NOT NULL,
    type ENUM('text', 'image') NOT NULL,
    content TEXT NOT NULL,                  -- Text message or image path
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (id_sos) REFERENCES gk_sos(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id),
    INDEX idx_sos_messages (id_sos, created_at)
);
```

### `gk_disaster_reports` (Disaster Potential Reports)

```sql
CREATE TABLE gk_disaster_reports (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_user BIGINT UNSIGNED NOT NULL,
    id_destinasi BIGINT UNSIGNED NOT NULL,
    id_booking CHAR(36) NULLABLE,
    potensi_bencana VARCHAR(255) NOT NULL,  -- Type of potential disaster
    deskripsi TEXT NOT NULL,                -- Detailed description
    lokasi VARCHAR(255) NOT NULL,           -- Location description (text)
    latitude DECIMAL(10, 8) NULLABLE,
    longitude DECIMAL(11, 8) NULLABLE,
    lampiran VARCHAR(255) NULLABLE,         -- Image attachment path
    status ENUM('pending', 'verified', 'broadcast', 'resolved', 'rejected') DEFAULT 'pending',
    verified_by BIGINT UNSIGNED NULLABLE,
    verified_at TIMESTAMP NULLABLE,
    notes TEXT NULLABLE,                    -- Admin notes
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id),
    FOREIGN KEY (id_destinasi) REFERENCES destinasis(id),
    FOREIGN KEY (id_booking) REFERENCES gk_bookings(id) ON DELETE SET NULL,
    INDEX idx_status (status, id_destinasi)
);
```

---

## 10. API Endpoint Design

### GPS Tracking Endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/api/tracking/gps` | Sanctum | Upload single GPS position |
| POST | `/api/tracking/gps/batch` | Sanctum | Upload batch GPS positions (offline sync) |
| GET | `/api/tracking/my-position` | Sanctum | Get my last recorded position |

### Checkpoint/Trail Tracking Endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/api/tracking/checkpoint/qr` | Sanctum | Check-in via QR code scan |
| POST | `/api/tracking/checkpoint/gps` | Sanctum | Check proximity to posts |
| POST | `/api/tracking/checkpoint/manual` | Sanctum | Manual check-in at post |
| GET | `/api/tracking/progress/{booking_id}` | Sanctum | Get trail progress |
| GET | `/api/tracking/posts/{gate_id}` | Sanctum | Get all posts for a route |

### Emergency Endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/api/emergency/trigger` | Sanctum | Hiker triggers emergency alert |
| GET | `/api/emergency/active` | Sanctum | Get active emergencies for my destination |
| PUT | `/api/emergency/{id}/resolve` | Sanctum (Admin) | Resolve emergency |

### SOS Endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/api/sos/trigger` | Sanctum | Trigger SOS panic button |
| GET | `/api/sos/active` | Sanctum | Get my active SOS |
| POST | `/api/sos/chat/{sos_id}/send` | Sanctum | Send chat message |
| GET | `/api/sos/chat/{sos_id}/messages` | Sanctum | Get chat history (paginated) |
| GET | `/api/sos/call-options` | Sanctum | Get rescue team contact info |
| POST | `/api/sos/disaster-report` | Sanctum | Submit disaster report |
| GET | `/api/sos/disaster-reports` | Sanctum | Get my disaster reports |

### Admin Endpoints (Web)

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/admin/monitoring` | Web+Role | Live monitoring dashboard |
| GET | `/admin/emergency` | Web+Role | Emergency management |
| POST | `/admin/emergency/broadcast` | Web+Role | Broadcast emergency to hikers |
| GET | `/admin/sos` | Web+Role | SOS management list |
| GET | `/admin/sos/{id}` | Web+Role | SOS detail + chat |
| PUT | `/admin/sos/{id}/status` | Web+Role | Update SOS status |
| GET | `/admin/disaster-reports` | Web+Role | Disaster reports list |
| PUT | `/admin/disaster-reports/{id}/verify` | Web+Role | Verify/reject report |
| GET | `/admin/posts` | Web+Role | Manage trail posts |
| POST | `/admin/posts` | Web+Role | Create post |
| PUT | `/admin/posts/{id}` | Web+Role | Update post |
| DELETE | `/admin/posts/{id}` | Web+Role | Delete post |

---

## 11. Technical Decisions & Trade-offs

### Decision 1: WebSocket Technology

| Option | Pros | Cons | Decision |
|--------|------|------|----------|
| **Laravel Reverb** | Native L11, self-hosted, no external dependency, free | Requires server resources, manage yourself | **Recommended for production** |
| Pusher | Managed, easy setup, reliable | Costs money at scale, external dependency | Good for MVP/prototype |
| Polling (fallback) | No WebSocket needed, works everywhere | Higher latency (5-10s), more server load | **Fallback only** |

**Decision**: Use **Laravel Reverb** as primary with **polling fallback** for environments where WebSocket fails.

### Decision 2: GPS Update Frequency

| Frequency | Battery Impact | Data Volume | Accuracy |
|-----------|---------------|-------------|----------|
| Every 10s | Very High | ~8,640/day | Excellent |
| Every 30s | High | ~2,880/day | Very Good |
| **Every 60s** | Moderate | ~1,440/day | Good |
| Every 5min | Low | ~288/day | Acceptable |

**Decision**: Default **60 seconds** during active hiking, **5 minutes** when stationary (detected by mobile). Configurable per destination.

### Decision 3: Image Storage for SOS/Disaster

| Option | Pros | Cons |
|--------|------|------|
| Local storage | Simple, no cost, fast | Single point of failure, disk space |
| **S3/MinIO** | Scalable, redundant, CDN-ready | Cost, complexity |

**Decision**: Start with **local storage** (`storage/app/public/`), migrate to S3 when scaling. Use Laravel's filesystem abstraction for easy switch.

### Decision 4: SMS for Emergency Contact

| Option | Pros | Cons |
|--------|------|------|
| Twilio | Reliable, global | Expensive for Indonesia |
| **Local SMS Gateway (Zenziva/WA Gateway)** | Cheap, local numbers | Less reliable |
| WhatsApp Business API | Free-ish, popular in Indonesia | Complex setup, approval needed |

**Decision**: Phase 1 uses **WhatsApp deep link** (free, immediate). Phase 2 adds **SMS gateway** for critical SOS (when WhatsApp unavailable).

### Decision 5: Offline Handling

The mountain has limited connectivity. Strategy:
1. Mobile app queues GPS data locally (SQLite/SharedPrefs)
2. Batch upload when connectivity resumes
3. QR scan records locally with timestamp, syncs later
4. SOS attempts immediate send, retries every 10s, falls back to SMS

---

## Implementation Priority & Timeline Estimate

| Phase | Priority | Estimated Effort | Dependencies |
|-------|----------|-----------------|--------------|
| Phase 1: Foundation | Critical | 3-4 days | None |
| Phase 2: Pesan Darurat | High | 4-5 days | Phase 1 |
| Phase 3: Pelacakan Jejak | High | 5-6 days | Phase 1 |
| Phase 4: SOS System | Critical | 6-8 days | Phase 1, 2 |
| Phase 5: Admin Dashboard | Medium | 4-5 days | Phase 2, 3, 4 |
| Phase 6: Testing | High | 3-4 days | All phases |

**Total Estimated Effort**: ~25-32 days (1 developer)

---

## File Structure (New Files)

```
app/
├── Events/
│   ├── EmergencyTriggered.php
│   ├── EmergencyBroadcast.php
│   ├── SOSTriggered.php
│   └── SOSMessageSent.php
├── Http/Controllers/
│   ├── API/
│   │   ├── TrackingController.php
│   │   ├── CheckpointController.php
│   │   ├── EmergencyController.php
│   │   ├── SOSController.php
│   │   └── DisasterReportController.php
│   └── etiket/admin/
│       ├── monitoring/
│       │   └── MonitoringController.php
│       ├── emergency/
│       │   └── EmergencyAdminController.php
│       ├── sos/
│       │   └── SOSAdminController.php
│       └── posts/
│           └── PostController.php
├── Models/
│   ├── GkPost.php
│   ├── GkTracking.php
│   ├── GkCheckpointLog.php
│   ├── GkEmergencyMessage.php
│   ├── GkSos.php
│   ├── GkSosChat.php
│   └── GkDisasterReport.php
├── Services/
│   ├── TrackingService.php
│   ├── EmergencyService.php
│   ├── SOSService.php
│   └── GeoService.php          (Haversine, proximity calc)
database/migrations/
│   ├── 70_gk_posts.php
│   ├── 71_gk_tracking.php
│   ├── 72_gk_checkpoint_logs.php
│   ├── 73_gk_emergency_messages.php
│   ├── 74_gk_sos.php
│   ├── 75_gk_sos_chats.php
│   └── 76_gk_disaster_reports.php
resources/views/etiket/admin/
│   ├── monitoring/
│   │   └── index.blade.php
│   ├── emergency/
│   │   ├── index.blade.php
│   │   └── detail.blade.php
│   ├── sos/
│   │   ├── index.blade.php
│   │   └── detail.blade.php
│   └── posts/
│       ├── index.blade.php
│       └── form.blade.php
routes/
│   ├── api/
│   │   ├── routeTracking.php
│   │   ├── routeEmergency.php
│   │   └── routeSOS.php
│   ├── web/
│   │   └── routeAdmins.php     (extend existing)
│   └── channels.php            (WebSocket channel auth)
```

---

## Notes & Risks

1. **Mountain connectivity**: GPS tracking and SOS depend on mobile signal. Design for offline-first.
2. **Battery drain**: Frequent GPS polling drains battery. Mobile app must optimize (use significant location changes, not continuous polling).
3. **False SOS**: Implement confirmation UX and admin false-alarm workflow to prevent alert fatigue.
4. **Data volume**: GPS tracking generates significant data. Plan for archival/cleanup of old tracking data (e.g., delete after 30 days post-checkout).
5. **Security**: SOS chat images could contain sensitive content. Implement basic moderation or restrict to admin-only viewing.
6. **Scalability**: WebSocket connections scale differently than HTTP. Monitor Reverb server capacity during peak hiking season.
