# Phase 4 Implementation Report: SOS System

> **Date**: 2026-05-11  
> **Plan**: `docs/plan-emergency-tracking-sos.md` — Phase 4  
> **Status**: ✅ Complete

---

## Summary

Phase 4 implements the SOS panic button system: trigger with cooldown, two-way chat messaging, WhatsApp call redirect, disaster reporting, and admin response panel with status lifecycle management.

---

## API Endpoints

### 1. SOS Trigger (Panic Button)

```
POST /api/sos/trigger
Auth: Bearer Token (Sanctum)
```

**Request Body:**
```json
{
    "latitude": -1.6974,
    "longitude": 101.2642,
    "severity": "high",
    "message": "Saya terjatuh dan tidak bisa bergerak"
}
```

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| latitude | numeric | ✅ | -90 to 90 |
| longitude | numeric | ✅ | -180 to 180 |
| severity | string | ✅ | low, medium, high |
| message | string | ❌ | max: 1000 |

**Success Response (201):**
```json
{
    "success": true,
    "message": "SOS berhasil dikirim. Bantuan sedang dalam perjalanan.",
    "data": {
        "sos_id": 5,
        "status": "active",
        "created_at": "2026-05-11T10:00:00.000Z",
        "rescue_contact": {
            "phone": "+6281234567890",
            "whatsapp_link": "https://wa.me/6281234567890"
        }
    },
    "errors": null
}
```

**Error Responses:**
- `403` — No active hiking
- `429` — Cooldown active (`{"wait_seconds": 180}`)

**Side Effects:** Broadcasts `SOSTriggered` on `private-sos.{destinasi_id}`

---

### 2. Get Active SOS

```
GET /api/sos/active
Auth: Bearer Token (Sanctum)
```

**Success Response (200):**
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
        "created_at": "2026-05-11T10:00:00.000Z"
    },
    "errors": null
}
```

---

### 3. Send SOS Chat Message

```
POST /api/sos/chat/{sos_id}/send
Auth: Bearer Token (Sanctum)
Content-Type: multipart/form-data
```

**Request Body (text):**
```json
{
    "type": "text",
    "content": "Saya di dekat pohon besar setelah Pos 2"
}
```

**Request Body (image):**
```
type=image
image=<file upload, jpeg/png, max 5MB>
```

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| type | string | ✅ | text, image |
| content | string | ✅ (if text) | max: 2000 |
| image | file | ✅ (if image) | jpeg/png/jpg, max: 5120KB |

**Success Response (201):**
```json
{
    "success": true,
    "message": "Pesan terkirim",
    "data": {
        "id": 12,
        "sender_type": "hiker",
        "type": "text",
        "content": "Saya di dekat pohon besar setelah Pos 2",
        "created_at": "2026-05-11T10:05:00.000Z"
    },
    "errors": null
}
```

**Side Effects:** Broadcasts `SOSMessageSent` on `private-sos-chat.{sos_id}`

---

### 4. Get SOS Chat Messages

```
GET /api/sos/chat/{sos_id}/messages
Auth: Bearer Token (Sanctum)
```

**Success Response (200):**
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
                "content": "Tolong saya terjatuh",
                "is_read": true,
                "created_at": "2026-05-11T10:00:00.000Z"
            },
            {
                "id": 11,
                "sender_type": "admin",
                "sender_name": "Admin",
                "type": "text",
                "content": "Tim SAR sedang menuju lokasi Anda",
                "is_read": false,
                "created_at": "2026-05-11T10:02:00.000Z"
            }
        ],
        "pagination": {
            "current_page": 1,
            "last_page": 1,
            "total": 2
        }
    },
    "errors": null
}
```

---

### 5. Get Call Options (WhatsApp Redirect)

```
GET /api/sos/call-options
Auth: Bearer Token (Sanctum)
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Kontak darurat",
    "data": [
        {
            "name": "Tim SAR TNKS",
            "phone": "+6281234567890",
            "whatsapp_link": "https://wa.me/6281234567890?text=SOS%20-%20John%20Doe%20butuh%20bantuan.%20Booking%3A%20uuid-123",
            "tel_link": "tel:+6281234567890"
        }
    ],
    "errors": null
}
```

---

### 6. Submit Disaster Report

```
POST /api/sos/disaster-report
Auth: Bearer Token (Sanctum)
Content-Type: multipart/form-data
```

**Request Body:**
```json
{
    "potensi_bencana": "Longsor",
    "deskripsi": "Terlihat retakan tanah besar di jalur setelah Pos 2",
    "lokasi": "Antara Pos 2 dan Pos 3",
    "latitude": -1.6950,
    "longitude": 101.2630,
    "lampiran": "<image file>"
}
```

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| potensi_bencana | string | ✅ | max: 255 |
| deskripsi | string | ✅ | max: 2000 |
| lokasi | string | ✅ | max: 255 |
| latitude | numeric | ❌ | -90 to 90 |
| longitude | numeric | ❌ | -180 to 180 |
| lampiran | file | ❌ | jpeg/png/jpg, max: 5120KB |

**Success Response (201):**
```json
{
    "success": true,
    "message": "Laporan bencana berhasil dikirim",
    "data": {
        "id": 3,
        "status": "pending",
        "created_at": "2026-05-11T11:00:00.000Z"
    },
    "errors": null
}
```

**Side Effects:** Broadcasts `DisasterReported` on `private-sos.{destinasi_id}`

---

### 7. Get My Disaster Reports

```
GET /api/sos/disaster-reports
Auth: Bearer Token (Sanctum)
```

**Success Response (200):**
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
            "created_at": "2026-05-11T11:00:00.000Z"
        }
    ],
    "errors": null
}
```

---

## Admin Web Endpoints

### 8. SOS List

```
GET /admin/sos
Auth: Web Session + check.role:admin
```

Renders SOS list with status badges, pending disaster reports section.

### 9. SOS Detail + Chat

```
GET /admin/sos/{id}
Auth: Web Session + check.role:admin
```

Renders SOS detail with hiker info, Leaflet map, chat interface, status action buttons.

### 10. Update SOS Status

```
PUT /admin/sos/{id}/status
Auth: Web Session + check.role:admin
```

| Field | Type | Required | Values |
|-------|------|----------|--------|
| status | string | ✅ | acknowledged, dispatched, resolved, false_alarm |
| resolution_notes | string | ❌ | max: 1000 |

**Side Effects:** Broadcasts `SOSStatusUpdated` on `private-sos.{destinasi_id}` + `private-sos-chat.{sos_id}`

### 11. Verify Disaster Report

```
PUT /admin/disaster-report/{id}/verify
Auth: Web Session + check.role:admin
```

| Field | Type | Required | Values |
|-------|------|----------|--------|
| status | string | ✅ | verified, rejected |
| notes | string | ❌ | max: 1000 |

---

## SOS Status Lifecycle

```
active → acknowledged → dispatched → resolved
                                   → false_alarm
```

---

## WebSocket Events

| Event | Channel | When |
|-------|---------|------|
| `sos.triggered` | `private-sos.{destinasi_id}` | Hiker presses SOS |
| `sos.message.sent` | `private-sos-chat.{sos_id}` | Chat message sent |
| `sos.status.updated` | `private-sos.{did}` + `private-sos-chat.{sid}` | Admin changes status |
| `disaster.reported` | `private-sos.{destinasi_id}` | Disaster report submitted |

---

## Files Created/Modified

```
# New
app/Http/Controllers/API/SOSController.php
app/Http/Controllers/API/SOSChatController.php
app/Http/Controllers/API/SOSCallController.php
app/Http/Controllers/etiket/admin/sos/SOSAdminController.php
resources/views/etiket/admin/sos/index.blade.php
resources/views/etiket/admin/sos/detail.blade.php
routes/api/routeSOS.php

# Modified
routes/api.php (added routeSOS include)
routes/web/routeAdmins.php (added SOS admin routes)
```

---

## Verification

- ✅ All 4 controllers pass syntax check
- ✅ 11 SOS routes registered (7 API + 4 admin web)
- ✅ All 13 existing tests pass (no regressions)
- ✅ App boots cleanly (`php artisan optimize:clear`)
- ✅ SOS endpoints return 401 without auth (Sanctum protection confirmed)
- ✅ Admin routes protected by `check.role:admin` middleware
