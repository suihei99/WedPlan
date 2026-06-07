# WedPlan — Web Application v1.0.0

A focused, production-ready Laravel web application that provides wedding budgeting, vendor management, task scheduling, guest tracking, and expense reporting. This repository contains the web backend and REST API used by the WedPlan web UI and the companion mobile app.

**Status:** Active development. This README covers developer setup, architecture, testing, and deployment guidance.

**Repository:** [README.md](README.md)

**Tech stack:** Laravel (PHP 8.2+), MySQL, Tailwind CSS, Vite, Pest (testing)

**Quick links:**
- **Application entry:** public/index.php
- **Routes:** routes/web.php and routes/api.php
- **Primary models:** app/Models (Booking, BudgetCategory, Expense, Couple, Guest, Service, Task, User, Vendor)

**Getting started (developer)**

1. Clone the repository and install PHP dependencies:

```powershell
git clone <repo-url>
cd WebPlan
composer install
```

2. Copy environment and generate app key:

```powershell
copy .env.example .env
php artisan key:generate
```

3. Configure `.env` (database, mail, firebase keys). Then run migrations and seeders:

```powershell
php artisan migrate --seed
```

4. Install frontend dependencies and build assets (optional for API-only work):

```powershell
npm install
npm run dev
```

5. Run tests (use Pest):

```powershell
php artisan test --parallel --compact
```

Development notes

- Use Laravel conventions: controllers in `app/Http/Controllers`, requests in `app/Http/Requests`, and services in `app/Services`.
- Frontend assets are in `resources/js` and `resources/css`, built via Vite.
- Database factories live in `database/factories` and seeders in `database/seeders`.

Architecture overview

- The application follows a service-oriented controller pattern: controllers delegate business logic to services in `app/Services`.
- Models use Eloquent; prefer relationship methods and eager-loading to avoid N+1 queries.
- API routes are versioned under `routes/api.php` and protected with Sanctum where applicable.

Testing

- This project uses Pest (Pest.php). Create tests with `php artisan make:test --pest NameTest`.
- Run a focused test suite when making changes: `php artisan test --filter=NameTest --compact`.

Formatting and linting

- Run PHP Pint to format PHP code before commit: `vendor/bin/pint`.
- Run `npm run lint` if lint scripts are configured for JS/CSS.

Deployment

- Build assets: `npm run build`.
- Ensure `.env` production values and that `APP_ENV=production` and `APP_DEBUG=false`.
- Use queue workers for background processing; configure Supervisor (or similar) to keep workers running.

Contribution guidelines

- Open issues for bugs or features. Create PRs against `main` and include tests for code changes.
- Follow existing code style and run `vendor/bin/pint` before pushing.

Where to look next

- App services: [app/Services](app/Services)
- API controllers and resources: [app/Http/Controllers](app/Http/Controllers)
- Tests: [tests](tests)

If you need a user-oriented manual, see [UserManual.md](UserManual.md).