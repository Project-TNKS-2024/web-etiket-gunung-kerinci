# Phase 2 Implementation Report: Pesan Darurat (Emergency Message)

> **Date**: 2026-05-11  
> **Plan**: `docs/plan-emergency-tracking-sos.md` — Phase 2  
> **Status**: ✅ Complete

---

## Summary

Phase 2 implements the Emergency Message system: GPS tracking upload from mobile, hiker emergency trigger, admin broadcast to hikers, and the admin emergency dashboard.

---

## API Endpoints

### 1. GPS Upload (Single)

```
POST /api/tracking/gps
Auth: Bearer Token (Sanctum)
```

**Request Body:**
```json
{
    "latitude": -1.6974,
    "longitude": 101.2642,
    "altitude": 2800.5,
    "accuracy": 12.3,
    "battery_level": 75,
    "recorded_at": "2026-05-11T10:30:00Z"
}
```

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| latitude | numeric | ✅ | -90 to 90 |
| longitude | numeric | ✅ | -180 to 180 |
| altitude | numeric | ❌ | - |
| accuracy | numeric | ❌ | min: 0 |
| battery_level | integer | ❌ | 0–100 |
| recorded_at | date | ❌ | defaults to now |

**Success Response (201):**
```json
{
    "success": true,
    "message": "GPS berhasil direkam",
    "data": {
        "id": 42,
        "server_time": "2026-05-11T10:30:05.123Z"
    },
    "errors": null
}
```

**Error Responses:**
- `403` — No active hiking (status_booking ≠ 6)
- `429` — Rate limited (< 30 seconds since last upload)
- `422` — Validation error

---

### 2. GPS Upload (Batch / Offline Sync)

```
POST /api/tracking/gps/batch
Auth: Bearer Token (Sanctum)
```

**Request Body:**
```json
{
    "positions": [
        {
            "latitude": -1.6974,
            "longitude": 101.2642,
            "altitude": 2800.5,
            "accuracy": 12.3,
            "battery_level": 75,
            "recorded_at": "2026-05-11T10:30:00Z"
        },
        {
            "latitude": -1.6980,
            "longitude": 101.2650,
            "altitude": 2850.0,
            "accuracy": 8.5,
            "battery_level": 72,
            "recorded_at": "2026-05-11T10:31:00Z"
        }
    ]
}
```

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| positions | array | ✅ | min: 1, max: 100 |
| positions.*.latitude | numeric | ✅ | -90 to 90 |
| positions.*.longitude | numeric | ✅ | -180 to 180 |
| positions.*.altitude | numeric | ❌ | - |
| positions.*.accuracy | numeric | ❌ | min: 0 |
| positions.*.battery_level | integer | ❌ | 0–100 |
| positions.*.recorded_at | date | ✅ | - |

**Success Response (201):**
```json
{
    "success": true,
    "message": "Batch GPS berhasil direkam (2 posisi)",
    "data": {
        "inserted": 2,
        "server_time": "2026-05-11T10:35:00.000Z"
    },
    "errors": null
}
```

---

### 3. Get My Last Position

```
GET /api/tracking/my-position
Auth: Bearer Token (Sanctum)
```

**Success Response (200):**
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
        "recorded_at": "2026-05-11T10:30:00.000Z"
    },
    "errors": null
}
```

**No position yet (200):**
```json
{
    "success": true,
    "message": "Belum ada posisi terekam",
    "data": null,
    "errors": null
}
```

---

### 4. Trigger Emergency (Hiker)

```
POST /api/emergency/trigger
Auth: Bearer Token (Sanctum)
```

**Request Body:**
```json
{
    "title": "Tersesat di jalur",
    "description": "Saya tersesat setelah Pos 2, tidak bisa menemukan jalur kembali",
    "severity": "high",
    "latitude": -1.6980,
    "longitude": 101.2650
}
```

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| title | string | ✅ | max: 255 |
| description | string | ✅ | max: 2000 |
| severity | string | ✅ | low, medium, high, critical |
| latitude | numeric | ❌ | -90 to 90 (falls back to last GPS) |
| longitude | numeric | ❌ | -180 to 180 (falls back to last GPS) |

**Success Response (201):**
```json
{
    "success": true,
    "message": "Pesan darurat berhasil dikirim",
    "data": {
        "id": 7,
        "status": "active",
        "created_at": "2026-05-11T10:35:00.000Z"
    },
    "errors": null
}
```

**Side Effects:**
- Broadcasts `EmergencyTriggered` event on `private-emergency.{destinasi_id}` channel
- Admin dashboard receives real-time notification

---

### 5. Get Active Emergencies (Hiker)

```
GET /api/emergency/active
Auth: Bearer Token (Sanctum)
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Success",
    "data": [
        {
            "id": 7,
            "type": "admin_broadcast",
            "title": "Cuaca Buruk - Turun Segera",
            "description": "Badai diperkirakan tiba dalam 2 jam. Semua pendaki diminta turun.",
            "severity": "critical",
            "latitude": null,
            "longitude": null,
            "created_at": "2026-05-11T08:00:00.000Z"
        },
        {
            "id": 5,
            "type": "hiker_alert",
            "title": "Pendaki cedera di Pos 3",
            "description": "Ada pendaki cedera kaki di dekat Pos 3",
            "severity": "medium",
            "latitude": "-1.69500000",
            "longitude": "101.26300000",
            "created_at": "2026-05-11T07:30:00.000Z"
        }
    ],
    "errors": null
}
```

---

## Admin Web Endpoints

### 6. Emergency Dashboard

```
GET /admin/emergency
Auth: Web Session + check.role:admin
```

Renders Blade view with:
- Leaflet map showing active emergency locations
- Table of all emergencies (paginated, sorted by status then date)
- Broadcast modal form
- Acknowledge/Resolve action buttons

---

### 7. Broadcast Emergency (Admin)

```
POST /admin/emergency/broadcast
Auth: Web Session + check.role:admin
Content-Type: application/x-www-form-urlencoded
```

| Field | Type | Required |
|-------|------|----------|
| id_destinasi | integer | ✅ (must be assigned to admin) |
| title | string | ✅ (max: 255) |
| description | string | ✅ (max: 2000) |
| severity | string | ✅ (low/medium/high/critical) |
| _token | string | ✅ (CSRF) |

**Side Effects:**
- Creates `gk_emergency_messages` record with type `admin_broadcast`
- Broadcasts `EmergencyBroadcast` event to all hikers on `private-emergency.{destinasi_id}`

**Response:** Redirect back with flash message

---

### 8. Acknowledge Emergency

```
PUT /admin/emergency/{id}/acknowledge
Auth: Web Session + check.role:admin
```

Updates status from `active` → `acknowledged`. Requires CSRF + `_method=PUT`.

---

### 9. Resolve Emergency

```
PUT /admin/emergency/{id}/resolve
Auth: Web Session + check.role:admin
```

Updates status to `resolved` with timestamp. Requires CSRF + `_method=PUT`.

---

## WebSocket Events (Real-time)

| Event | Channel | When |
|-------|---------|------|
| `emergency.triggered` | `private-emergency.{destinasi_id}` | Hiker triggers emergency |
| `emergency.broadcast` | `private-emergency.{destinasi_id}` | Admin broadcasts alert |
| `tracking.updated` | `private-tracking.{destinasi_id}` | GPS position uploaded |

---

## Files Created/Modified

```
# New
app/Http/Controllers/API/TrackingController.php
app/Http/Controllers/API/EmergencyController.php
app/Http/Controllers/etiket/admin/emergency/EmergencyAdminController.php
resources/views/etiket/admin/emergency/index.blade.php
routes/api/routeTracking.php
routes/api/routeEmergency.php

# Modified
routes/api.php (added includes)
routes/web/routeAdmins.php (added emergency routes)
```

---

## Verification

- ✅ 9 routes registered (confirmed via `php artisan route:list`)
- ✅ All controllers pass syntax check
- ✅ App boots cleanly (`php artisan optimize:clear`)
- ✅ API routes protected by `auth:sanctum` middleware
- ✅ Admin routes protected by `check.role:admin` middleware
