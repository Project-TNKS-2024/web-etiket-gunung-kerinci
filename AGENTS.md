# AGENTS.md

## Project Overview

E-Tiket Gunung Kerinci — a web-based e-ticketing system for Taman Nasional Kerinci Seblat (TNKS) mountain hiking permits. Built with Laravel 11, Blade SSR views, Sanctum API for mobile, and Midtrans payment gateway.

## Tech Stack

- **Framework**: Laravel 11 (PHP 8.2+)
- **Views**: Blade templates (`resources/views/`)
- **API Auth**: Laravel Sanctum (token-based for mobile app)
- **Roles/Permissions**: Spatie Laravel Permission
- **Payment**: Midtrans
- **Frontend Assets**: Vite, Bootstrap 5.3.3, FontAwesome 6.5.2, DataTables
- **Database**: MySQL
- **Language**: Indonesian (id) primary, English fallback

## Project Structure

```
app/
├── Http/Controllers/
│   ├── API/              # Mobile API (Sanctum-protected)
│   ├── homepage/         # Public pages & booking flow
│   ├── etiket/admin/     # Admin panel controllers
│   ├── etiket/auth/      # Web auth (login, register, reset)
│   ├── etiket/user/      # User dashboard
│   └── helper/           # Shared utilities, Midtrans, uploads
├── Models/               # Eloquent models (gk_booking, gk_pendaki, etc.)
├── Mail/                 # Mailable classes
└── Helpers/              # Global helper functions
bootstrap/                # Laravel bootstrap + cached config
config/                   # App configuration
database/
├── migrations/           # Numbered migration files (00–60)
├── seeders/              # Database seeders
└── factories/            # Model factories
public/                   # Web root (index.php, assets, uploads)
resources/views/
├── homepage/             # Public-facing Blade views
├── etiket/admin/         # Admin panel views
├── etiket/auth/          # Auth views
├── etiket/user/          # User dashboard views
└── email/                # Email templates
routes/
├── web.php               # Main web routes
├── api.php               # API routes
├── web/routeAdmins.php   # Admin route group
├── web/routeAuth.php     # Auth route group
├── web/routeUsers.php    # User route group
├── api/routeAuth.php     # API auth routes
├── api/routeUser.php     # API user routes
└── api/routeDomisili.php # API domisili/region routes
storage/                  # Logs, cache, sessions, uploads
docs/                     # Planning documents
```

## Commands

- Install PHP deps: `composer install`
- Install JS deps: `npm install`
- Run dev server: `php artisan serve`
- Run Vite: `npm run dev`
- Build assets: `npm run build`
- Run tests: `php artisan test`
- Run migrations: `php artisan migrate`
- Seed database: `php artisan db:seed`
- Clear cache: `php artisan optimize:clear`

## Source Of Truth

- `docs/api.md`: API endpoint documentation (Sanctum auth, response format, endpoints).
- `docs/plan-emergency-tracking-sos.md`: Emergency message, trail tracking, and SOS feature plan.
- `docs/plan-websocket.md`: Laravel Reverb WebSocket implementation plan (foundation for real-time features).
- `database/migrations/`: Database schema (numbered prefix = execution order).

## Architecture Rules

- Follow Laravel conventions: controllers in `app/Http/Controllers/`, models in `app/Models/`, views in `resources/views/`.
- Keep controllers thin; move complex logic to services or model methods.
- Use Eloquent relationships and scopes; avoid raw queries unless performance-critical.
- Use Laravel's built-in validation (`$request->validate()`) for all input.
- Use Blade components and `@include` for reusable view partials.
- Route groups must use appropriate middleware (`auth`, `check.role`, `logger`).

## Security Rules

- Never hard-code credentials; use `.env` and `config()` helpers.
- Never commit `.env` — it is in `.gitignore`.
- All POST/PUT/PATCH/DELETE forms must include `@csrf`.
- Use `$request->validate()` or Form Request classes for input validation.
- File uploads: validate MIME type server-side, use randomized filenames, store outside web root when possible.
- API endpoints must be protected with Sanctum middleware (`auth:sanctum`).
- Admin routes must check role via `check.role` or Spatie `role`/`permission` middleware.
- Do not use GET for destructive actions.

## Database Rules

- Use Laravel migrations for all schema changes.
- Migration filenames use numeric prefix for ordering (e.g., `41_gk_pendaki.php`).
- Model naming follows existing convention: `gk_booking`, `gk_pendaki`, `gk_paket_tiket`, etc.
- Use Eloquent relationships (`hasMany`, `belongsTo`, etc.) instead of manual joins.
- Soft deletes where appropriate for booking/user data.

## API Conventions

- Base path: `/api`
- Auth: Bearer token via Sanctum.
- Standard JSON response format:
  ```json
  { "success": bool, "message": "...", "data": {...}, "errors": {...} }
  ```
- Use `ApiResponse` helper (`app/Http/Controllers/helper/ApiResponse.php`) for consistent responses.
- API fallback returns 404 JSON for undefined routes.

## Frontend Conventions

- Views use Blade templating with `@extends` and `@section`.
- Admin panel uses Modernize template (`public/modernize/`).
- Public pages use custom Bootstrap-based template (`resources/views/homepage/template/`).
- Static assets in `public/assets/`, `public/bootstrap-5.3.3-dist/`, `public/fontawesome-free-6.5.2-web/`.
- DataTables used for admin data tables (`public/DataTables/`).
- Uploaded files go to `public/upload/`.

## Planned Features (from docs/)

- **WebSocket**: Laravel Reverb for real-time admin notifications.
- **Emergency Tracking**: GPS trail tracking for active hikers.
- **SOS System**: Emergency alert from mobile app to admin dashboard.
- These features depend on WebSocket foundation being implemented first.

## Work Discipline

### Verification Before Marking Done

- Never mark a task as complete without cross-checking: run the relevant test, load the page, or confirm the feature works end-to-end.
- After implementing a route or controller change, verify with `php artisan route:list` or a manual request.
- After migration changes, run `php artisan migrate` and confirm no errors.
- After Blade view changes, load the page in browser to confirm no rendering errors.
- After API changes, test the endpoint with a sample request and verify the response matches the standard format.
- If a task cannot be verified (e.g., missing database, missing env), document the limitation clearly instead of marking it done.

### Git Commit

- Commit every completed phase after verification passes; do not leave finished work uncommitted.
- Before committing, inspect `git status` and `git diff` to ensure only related files are included.
- Never commit `.env`, `storage/logs/*.log`, `vendor/`, or `node_modules/`.
- Use descriptive commit messages in Indonesian or English with a concise subject line.
- Prefer staging specific files (`git add <file>`) over `git add .` to avoid accidental inclusions.
- Do not amend, squash, reset, force-push, or rewrite history without explicit user permission.
- Do not push directly to main/master unless explicitly asked.

### Code Quality

- Run `php artisan test` after changes to models, controllers, or business logic.
- Run `npm run build` after modifying frontend assets to confirm Vite compiles without errors.
- Clear cached config (`php artisan optimize:clear`) when modifying config files or `.env`.
- Check for N+1 queries when adding Eloquent relationships to views or API responses.
- Ensure new routes have proper middleware (auth, role check, CSRF) before considering them done.

### Communication

- If an approach fails twice, stop and explain the root cause before trying a different approach.
- When a task has side effects on other features (e.g., migration changes affecting existing data), flag it before proceeding.
- Report any security concern found during implementation immediately.
