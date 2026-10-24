# SanaGo

SanaGo is a multi-tenant hospital management system built for hospitals and clinics in Cameroon. A single Laravel application serves two surfaces:

- **Platform console** — the landlord side. SanaGo staff provision hospitals, manage plans and subscriptions, review billing, handle support tickets, and impersonate hospital administrators for support.
- **Hospital workspace** — an isolated tenant application on each hospital subdomain. Staff manage patients, appointments, admissions, clinical records, diagnostics, pharmacy, inventory, and finance.

Each hospital has its own database, users, roles, and records. The central database holds only platform data (tenants, domains, plans, subscriptions, billing, support, audit, and landlord users).

## Features

- Patient management, appointments, admissions, wards/beds, emergencies
- Consultations, vitals, prescriptions, surgeries, certificates
- Laboratory and radiology requests/results
- Pharmacy inventory, batches, dispensing, supplies
- Invoicing, cash register (Caisse), expenditures, revenue reporting
- Insurance payers, patient insurance, and claims
- Doctor-specific and general service pricing
- Platform: tenant provisioning, plans, subscriptions, lifecycle billing, support, announcements, audit logs, health checks
- Hospital suspension and subscription gating with billing/support escape hatches
- One-time cross-domain support impersonation
- RBAC with Spatie permissions, activity logs, media attachments
- English and French locales

## Stack

| Layer            | Technology                                |
| ---------------- | ----------------------------------------- |
| Backend          | Laravel 13, PHP 8.3+                      |
| Frontend         | Inertia.js 3, React 19, TypeScript 5.7    |
| Styling          | Tailwind CSS 4                            |
| Build            | Vite 8, Bun                               |
| Multi-tenancy    | `stancl/tenancy` 3                        |
| Auth/permissions | Laravel auth, `spatie/laravel-permission` |
| Media            | `spatie/laravel-medialibrary`             |
| Audit            | `spatie/laravel-activitylog`              |
| Routing helpers  | Laravel Wayfinder, Ziggy                  |
| Realtime         | Laravel Reverb / Echo                     |
| Tests            | Pest 4                                    |
| Formatting       | Pint (PHP), ESLint + Prettier (JS/TS)     |

## Requirements

- PHP 8.3+ with the SQLite extension
- Composer 2
- Bun 1.4+
- SQLite (default development database)

## Quick start

```bash
composer install

cp .env.example .env
# Set APP_URL=http://localhost:8000 in .env for local links
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed

bun install
composer dev
```

`php artisan migrate --seed` creates the central schema, then `TenantSeeder` provisions and seeds the two demo hospitals (including their tenant databases and demo data).

`composer dev` runs the HTTP server, queue listener, Reverb, log tail, and Vite together. Open the platform at <http://localhost:8000>.

> `composer setup` is a shorthand for dependency installation and the first asset build. It assumes `database/database.sqlite` already exists and does not seed the database. Run `php artisan db:seed` after it if you want the demo hospitals and accounts.

### Demo accounts

All seeded accounts use the password `password`.

| Surface               | URL                                         | Account                |
| --------------------- | ------------------------------------------- | ---------------------- |
| Platform console      | <http://localhost:8000/login>               | `admin@sanago.local`   |
| City General Hospital | <http://city-hospital.localhost:8000/login> | `admin@hospital.local` |
| Green Valley Clinic   | <http://green-valley.localhost:8000/login>  | `admin@hospital.local` |

Other platform roles seeded for permission testing: `tenant@sanago.local`, `billing@sanago.local`, `support@sanago.local`, `sales@sanago.local`.

Other hospital staff in each tenant: `reception@hospital.local`, `doctor@hospital.local`, `lab@hospital.local`, `pharma@hospital.local`.

`*.localhost` normally resolves to loopback automatically. If your environment does not resolve it, add the hosts to `/etc/hosts`:

```text
127.0.0.1 city-hospital.localhost green-valley.localhost
```

### Optional services

The defaults in `.env.example` assume MinIO/S3 for uploads and Reverb for realtime. Core workflows run without either.

- **Uploads without S3/MinIO:** set `FILESYSTEM_DISK=public` and `MEDIA_DISK=public`, then run `php artisan storage:link`.
- **Realtime:** `composer dev` starts Reverb. Set the `REVERB_APP_*` credentials, or use `composer dev-1` if you do not need realtime or queues. If Reverb is not configured, Echo is not loaded and notification screens fall back to polling.
- **Scheduler:** subscription renewal, expiry, reminders, and health checks are scheduled. Run `php artisan schedule:work` locally, and configure the cron entry in production.
- **Geocoding:** `docker-compose.yml` includes an optional Photon container; it is not required for core workflows.

## Common commands

| Command                              | Purpose                                   |
| ------------------------------------ | ----------------------------------------- |
| `composer dev`                       | Server + queue + Reverb + logs + Vite     |
| `composer dev-1`                     | Server + Vite only                        |
| `bun run dev`                        | Vite only                                 |
| `bun run build`                      | Production asset build                    |
| `php artisan migrate --seed`         | Central schema + demo tenants/data        |
| `php artisan db:seed`                | Re-run central seeders (idempotent)       |
| `php artisan test --compact`         | Pest test suite                           |
| `php artisan test --filter=SomeTest` | One test/filter                           |
| `composer test`                      | Pint check + full test suite              |
| `composer ci:check`                  | ESLint, Prettier, TypeScript, Pint, tests |
| `composer lint`                      | Fix PHP style with Pint                   |
| `bun run lint`                       | Fix JS/TS style with ESLint               |
| `bun run format`                     | Format frontend files with Prettier       |
| `bun run types:check`                | TypeScript type check                     |

## Project map

```text
app/
  Console/Commands/       Subscription lifecycle + platform health commands
  Enums/                  PHP-backed statuses and domain enums
  Http/Controllers/       Thin controllers (Auth, hospital modules, platform)
  Http/Middleware/        Tenant/subscription/plan gates, locale, Inertia
  Http/Requests/          Store/Update validation
  Models/                 Central and tenant Eloquent models
  Notifications/          Tenant lifecycle and platform notifications
  Observers/              Billing and notification side effects
  Policies/               Authorization rules
  Providers/              App + tenancy service providers
  Services/               Business logic by domain
  Support/                PlatformContext, TenantDatabase, error reporting
  Tenancy/                Custom SQLite tenant database manager

database/
  migrations/             Central schema
  migrations/tenant/      Per-hospital schema
  migrations/test/        Tenant tables for tests
  seeders/                Central + tenant demo data

resources/js/
  pages/                  Inertia pages, one per route
  Layouts/                App, SuperAdmin, Public, Context layouts
  Components/ui/          Shared UI kit
  Components/marketing/   Public-site components
  hooks/ lib/ types/      Shared frontend utilities
  routes/ actions/        Wayfinder-generated helpers (do not edit)

routes/
  web.php                 Shared/universal, marketing, platform routes
  tenant.php              Hospital workspace routes
  console.php             Scheduled commands

tests/
  Feature/                HTTP, tenancy, platform, service tests
  Unit/                   Unit tests
```

## How a request flows

1. `stancl/tenancy` resolves the host: a hospital subdomain activates that tenant and swaps the default database connection; a central domain keeps the central connection.
2. Tenant routes run `tenant.active` and `subscription.active`, so suspended hospitals and lapsed subscriptions lose application access while billing/support remain reachable.
3. Route middleware combines permissions (`can:...`), plan features (`feature:...`), and plan quotas (`plan.limit:...`).
4. Controllers validate through form requests, delegate business logic to `app/Services`, and render Inertia pages.
5. Models fire observers for billing/notification side effects; media and activity logs attach to the relevant models.

## Documentation

- [Documentation index](docs/README.md)
- [Getting started](docs/getting-started.md)
- [Project structure](docs/project-structure.md)
- [Architecture](docs/architecture.md)
- [Multi-tenancy](docs/tenancy.md)
- [Frontend](docs/frontend.md)
- [Testing](docs/testing.md)

## Conventions

- Keep controllers thin; put business logic in `app/Services/<Domain>`.
- Validate in `app/Http/Requests`, authorize with policies or `can:` middleware.
- Compose pages from `resources/js/Components/ui`; do not define one-off buttons, cards, tables, badges, or modals.
- Use semantic design tokens (`bg-surface`, `text-ink-muted`, `border-line`, `bg-accent-soft`) instead of palette steps or `dark:` variants.
- Use Wayfinder helpers from `@/routes` or `@/actions` for new links/actions. Existing pages also use Ziggy's global `route()`.
- Never hand-edit `resources/js/routes` or `resources/js/actions`; they are generated.
- Every change needs a passing test. Use Pest and the existing tenancy helpers.
