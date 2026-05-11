# Phase 6 Implementation Report: Testing & Deployment

> **Date**: 2026-05-11  
> **Plan**: `docs/plan-emergency-tracking-sos.md` — Phase 6  
> **Status**: ✅ Complete

---

## Summary

Phase 6 provides all deployment infrastructure: queue tables, Supervisor process management, Nginx WebSocket proxy, deployment script, and a test broadcast command for verification.

---

## Deployment Files

### `deploy/supervisor-reverb.conf`

Manages the Laravel Reverb WebSocket server process.

```ini
[program:reverb]
command=php /var/www/web-etiket-gunung-kerinci/artisan reverb:start --host=127.0.0.1 --port=8080
user=www-data
numprocs=1
autostart=true
autorestart=true
```

**Install:** `sudo cp deploy/supervisor-reverb.conf /etc/supervisor/conf.d/reverb.conf`

---

### `deploy/supervisor-queue-worker.conf`

Manages queue workers for broadcasting events.

```ini
[program:queue-worker]
command=php /var/www/web-etiket-gunung-kerinci/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
user=www-data
numprocs=2
autostart=true
autorestart=true
```

**Install:** `sudo cp deploy/supervisor-queue-worker.conf /etc/supervisor/conf.d/queue-worker.conf`

---

### `deploy/nginx-websocket.conf`

Nginx location blocks to proxy WebSocket connections to Reverb.

```nginx
location /app {
    proxy_pass http://127.0.0.1:8080;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "upgrade";
    ...
}
```

**Install:** Add contents inside your existing `server { }` block, then `sudo nginx -t && sudo systemctl reload nginx`

---

### `deploy/deploy.sh`

8-step deployment script:
1. `git pull`
2. `composer install --no-dev`
3. `npm install && npm run build`
4. `php artisan migrate --force`
5. Cache config/routes/views/events
6. `php artisan queue:restart`
7. Set permissions (www-data, 775)
8. Reload Supervisor + Nginx

**Usage:** `sudo bash deploy/deploy.sh`

---

## Test Broadcast Command

```bash
php artisan test:broadcast --destinasi=1
```

Sends a test `EmergencyTriggered` event to `private-emergency.{destinasi_id}`. Use this to verify:
- Reverb server is running
- Queue worker is processing
- Admin panel receives the event via Echo

---

## Queue Tables

| Table | Purpose |
|-------|---------|
| `jobs` | Pending queue jobs (broadcast events) |
| `failed_jobs` | Failed job records for debugging |
| `job_batches` | Batch job tracking |

All created via `php artisan migrate`.

---

## Production Setup Checklist

```bash
# 1. Copy Supervisor configs
sudo cp deploy/supervisor-reverb.conf /etc/supervisor/conf.d/reverb.conf
sudo cp deploy/supervisor-queue-worker.conf /etc/supervisor/conf.d/queue-worker.conf

# 2. Load and start processes
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start reverb
sudo supervisorctl start queue-worker:*

# 3. Add Nginx WebSocket proxy
# Edit /etc/nginx/sites-available/your-site.conf
# Add contents of deploy/nginx-websocket.conf inside server { }
sudo nginx -t
sudo systemctl reload nginx

# 4. Set production .env
BROADCAST_CONNECTION=reverb
REVERB_HOST="yourdomain.com"
REVERB_PORT=443
REVERB_SCHEME=https

# 5. Run migrations and seed permissions
php artisan migrate --force
php artisan db:seed --class=EmergencyPermissionSeeder

# 6. Cache and verify
php artisan config:cache
php artisan route:cache
php artisan test:broadcast --destinasi=1

# 7. Verify processes running
sudo supervisorctl status
```

---

## Verification

- ✅ Queue tables exist (jobs, failed_jobs, job_batches)
- ✅ 25 automated tests pass (47 assertions)
- ✅ App boots cleanly
- ✅ `test:broadcast` command executes and queues event
- ✅ All deployment configs are syntactically valid
