# Phase 3 Implementation Report: Pelacakan Jejak (Trail Tracking)

> **Date**: 2026-05-11  
> **Plan**: `docs/plan-emergency-tracking-sos.md` — Phase 3  
> **Status**: ✅ Complete

---

## Summary

Phase 3 implements the trail checkpoint tracking system: admin post management, QR code scanning, GPS proximity detection, manual check-in, and trail progress tracking per hiker.

---

## API Endpoints

### 1. QR Code Check-in

```
POST /api/tracking/checkpoint/qr
Auth: Bearer Token (Sanctum)
```

**Request Body:**
```json
{
    "qr_code_value": "POST-A1B2C3D4E5F6",
    "latitude": -1.6974,
    "longitude": 101.2642
}
```

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| qr_code_value | string | ✅ | Must exist in gk_posts |
| latitude | numeric | ❌ | -90 to 90 |
| longitude | numeric | ❌ | -180 to 180 |

**Success Response (201):**
```json
{
    "success": true,
    "message": "Check-in berhasil di Pos 1",
    "data": {
        "checkpoint_log_id": 15,
        "post": { "id": 3, "nama": "Pos 1", "urutan": 1 },
        "progress": { "completed": 1, "total": 5, "percentage": 20 }
    },
    "errors": null
}
```

**Error Responses:**
- `403` — No active hiking
- `404` — QR code not valid
- `409` — Already checked in at this post (`{ "checked_at": "..." }`)

---

### 2. GPS Proximity Detection

```
POST /api/tracking/checkpoint/gps
Auth: Bearer Token (Sanctum)
```

**Request Body:**
```json
{
    "latitude": -1.6974,
    "longitude": 101.2642,
    "accuracy": 12.5
}
```

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| latitude | numeric | ✅ | -90 to 90 |
| longitude | numeric | ✅ | -180 to 180 |
| accuracy | numeric | ❌ | min: 0 |

**Success Response (200):**
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "nearest_post": {
            "id": 3,
            "nama": "Pos 1",
            "distance_meters": 85.42,
            "within_radius": true,
            "radius_meter": 150
        },
        "all_posts": [
            { "id": 3, "nama": "Pos 1", "urutan": 1, "distance_meters": 85.42, "within_radius": true },
            { "id": 4, "nama": "Pos 2", "urutan": 2, "distance_meters": 1250.80, "within_radius": false },
            { "id": 5, "nama": "Puncak", "urutan": 3, "distance_meters": 3400.15, "within_radius": false }
        ]
    },
    "errors": null
}
```

**Note:** This endpoint does NOT auto-record check-in. The mobile app should show a confirmation button if `within_radius: true`.

---

### 3. Manual Check-in

```
POST /api/tracking/checkpoint/manual
Auth: Bearer Token (Sanctum)
```

**Request Body:**
```json
{
    "post_id": 3,
    "latitude": -1.6980,
    "longitude": 101.2650
}
```

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| post_id | integer | ✅ | Must exist in gk_posts |
| latitude | numeric | ✅ | -90 to 90 |
| longitude | numeric | ✅ | -180 to 180 |

**Success Response (201):**
```json
{
    "success": true,
    "message": "Check-in manual berhasil di Pos 1",
    "data": {
        "checkpoint_log_id": 16,
        "post": { "id": 3, "nama": "Pos 1", "urutan": 1 },
        "is_manual_override": false,
        "distance_from_post": 125.30,
        "progress": { "completed": 1, "total": 5, "percentage": 20 }
    },
    "errors": null
}
```

**Note:** `is_manual_override: true` when hiker is >500m from the post. Admin can review these.

---

### 4. Trail Progress

```
GET /api/tracking/progress/{booking_id}
Auth: Bearer Token (Sanctum)
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "booking_id": "uuid-booking-id",
        "gate": "Gate Kersik Tuo",
        "total_posts": 5,
        "hikers": [
            {
                "pendaki_id": "uuid-pendaki-1",
                "nama": "John Doe",
                "completed": 3,
                "total": 5,
                "percentage": 60,
                "checkpoints": [
                    { "post_id": 3, "nama": "Pos 1", "urutan": 1, "completed": true, "checked_at": "2026-05-11T08:00:00Z", "method": "qr" },
                    { "post_id": 4, "nama": "Pos 2", "urutan": 2, "completed": true, "checked_at": "2026-05-11T10:30:00Z", "method": "gps" },
                    { "post_id": 5, "nama": "Pos 3", "urutan": 3, "completed": true, "checked_at": "2026-05-11T13:00:00Z", "method": "manual" },
                    { "post_id": 6, "nama": "Shelter", "urutan": 4, "completed": false, "checked_at": null, "method": null },
                    { "post_id": 7, "nama": "Puncak", "urutan": 5, "completed": false, "checked_at": null, "method": null }
                ]
            }
        ]
    },
    "errors": null
}
```

---

### 5. Get Posts for a Route

```
GET /api/tracking/posts/{gate_id}
Auth: Bearer Token (Sanctum)
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Success",
    "data": [
        { "id": 3, "nama": "Pos 1", "urutan": 1, "latitude": "-1.69740000", "longitude": "101.26420000", "altitude": 2400, "radius_meter": 150 },
        { "id": 4, "nama": "Pos 2", "urutan": 2, "latitude": "-1.69500000", "longitude": "101.26300000", "altitude": 2800, "radius_meter": 150 },
        { "id": 5, "nama": "Puncak", "urutan": 3, "latitude": "-1.69200000", "longitude": "101.26100000", "altitude": 3805, "radius_meter": 200 }
    ],
    "errors": null
}
```

---

## Admin Web Endpoints

### 6. Posts Management

```
GET    /admin/posts          → List all posts (map + table)
POST   /admin/posts          → Create new post
PUT    /admin/posts/{id}     → Update post
DELETE /admin/posts/{id}     → Delete post
```

**Create/Update Fields:**

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| id_gate | integer | ✅ | Must exist in gk_gates |
| nama | string | ✅ | max: 255 |
| urutan | integer | ✅ | min: 1 |
| latitude | numeric | ✅ | -90 to 90 |
| longitude | numeric | ✅ | -180 to 180 |
| altitude | integer | ❌ | - |
| radius_meter | integer | ❌ | 50–500, default: 150 |
| detail | string | ❌ | max: 1000 |
| status | boolean | ❌ | (update only) |

**Auto-generated:** `qr_code_value` = `POST-{random12chars}` on create.

---

## Files Created/Modified

```
# New
app/Http/Controllers/API/CheckpointController.php
app/Http/Controllers/etiket/admin/posts/PostAdminController.php
resources/views/etiket/admin/posts/index.blade.php

# Modified
routes/api/routeTracking.php (added 5 checkpoint endpoints)
routes/web/routeAdmins.php (added 4 post CRUD routes)
```

---

## Verification

- ✅ 9 new routes registered (5 API checkpoint + 4 admin posts)
- ✅ All controllers pass syntax check
- ✅ App boots cleanly (`php artisan optimize:clear`)
- ✅ GeoService Haversine formula used for proximity detection
- ✅ Duplicate check-in prevention (unique per pendaki+post+booking)
- ✅ Manual override flagged when >500m from post
