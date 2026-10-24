# Getting started

## Prerequisites

- PHP 8.3+ with PDO SQLite (CI covers 8.3–8.5)
- Composer 2
- Bun 1.4+
- SQLite

## First-time setup

From the repository root:

```bash
composer install

cp .env.example .env
# Set APP_URL=http://localhost:8000 so generated links match the dev server
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed

bun install
composer dev
```

What each step does:

- `php artisan migrate --seed` creates the central schema and runs `DatabaseSeeder`, which seeds landlord roles/users, platform settings, the demo tenants, and subscriptions.
- `TenantSeeder` creates `database/tenants/city-hospital/database.sqlite` and `database/tenants/green-valley/database.sqlite`, runs the tenant migrations, and seeds hospital data.
- `composer dev` starts `php artisan serve`, the queue listener, Reverb, Pail, and Vite in parallel.

Open <http://localhost:8000>.

### Alternative: Composer setup script

`composer setup` installs dependencies, creates `.env`, generates the key, runs central migrations, installs frontend dependencies, and builds assets. It does **not** seed the database and does not explicitly create the SQLite file. Create the file first, then seed:

```bash
touch database/database.sqlite
composer setup
php artisan db:seed
```

## Environment notes

The committed `.env.example` is geared toward the full local stack:

- `DB_CONNECTION=sqlite` — central DB defaults to `database/database.sqlite`
- `SESSION_DRIVER=database`, `CACHE_STORE=database`, `QUEUE_CONNECTION=database`
- `FILESYSTEM_DISK=s3` and `MEDIA_DISK=s3` with MinIO at `http://localhost:9000`
- Reverb as the broadcast connection

If you do not run MinIO, switch uploads to the public disk:

```dotenv
FILESYSTEM_DISK=public
MEDIA_DISK=public
```

Then run:

```bash
php artisan storage:link
```

Realtime is optional. When `VITE_REVERB_APP_KEY` is absent, Echo is not imported and notification screens poll instead. `composer dev` starts Reverb, so set `REVERB_APP_ID`, `REVERB_APP_KEY`, and `REVERB_APP_SECRET`, or use `composer dev-1` when you do not need realtime or queues.

## Demo data and accounts

All seeded passwords are `password`.

Platform (central domain, <http://localhost:8000/login>):

| Email                  | Role            |
| ---------------------- | --------------- |
| `admin@sanago.local`   | super-admin     |
| `tenant@sanago.local`  | tenant-manager  |
| `billing@sanago.local` | billing-manager |
| `support@sanago.local` | support-agent   |
| `sales@sanago.local`   | sales-rep       |

Hospitals (<http://city-hospital.localhost:8000/login> and <http://green-valley.localhost:8000/login>):

| Email                      | Role      |
| -------------------------- | --------- |
| `admin@hospital.local`     | admin     |
| `reception@hospital.local` | reception |
| `doctor@hospital.local`    | doctor    |
| `lab@hospital.local`       | lab_tech  |
| `pharma@hospital.local`    | pharma    |

Each tenant gets its own copy of staff, patients, appointments, admissions, clinical records, lab, pharmacy, finance, and shift demo data.

## Development workflow

- Backend changes are picked up automatically by `php artisan serve`.
- Frontend changes are hot-reloaded by Vite.
- Queue jobs are processed by the `queue:listen` process started by `composer dev`.
- Reverb broadcasts are available through the same command.
- Laravel Pail tails application logs.

If you only need the app and Vite:

```bash
composer dev-1
```

If you prefer separate terminals:

```bash
php artisan serve
bun run dev
```

## Quality checks

Run these before handing work back:

```bash
composer lint          # Pint (fix)
composer lint:check    # Pint (check only)
bun run lint           # ESLint (fix)
bun run lint:check     # ESLint (check only)
bun run format         # Prettier (write)
bun run format:check   # Prettier (check only)
bun run types:check    # TypeScript
php artisan test --compact
```

`composer ci:check` runs the full frontend checks plus Pest.

## First task walkthrough

A typical feature touches these layers:

1. **Route** — add it to `routes/tenant.php` inside the authenticated group, or to `routes/web.php` for shared/platform routes.
2. **Controller** — create or extend a thin controller in `app/Http/Controllers`.
3. **Request** — add a `Store...Request` / `Update...Request` in `app/Http/Requests` for validation.
4. **Service** — put business logic in the matching `app/Services/<Domain>` class.
5. **Model / migration** — tenant models use `database/migrations/tenant`; central models use `database/migrations`.
6. **Page** — add a React page under `resources/js/pages` and wrap it in the correct layout.
7. **Test** — add a Pest feature test in `tests/Feature`, using the tenant helpers described in [Testing](testing.md).

Run Vite at least once after adding a route so Wayfinder regenerates `resources/js/routes` and `resources/js/actions`.

## Troubleshooting

- **`ViteException: Unable to locate file in Vite manifest`** — run `bun run dev` or `bun run build`.
- **Hospital subdomain does not resolve** — use `*.localhost` URLs, or add the hosts to `/etc/hosts`. Seeded domains are bare fragments (`city-hospital`), which expand to `city-hospital.localhost` in the browser.
- **403 on a hospital** — the tenant may be suspended, archived, or outside subscription grace. Reactivate it from the platform console. While suspended, `billing`, `support`, `notifications`, `logout`, `locale`, and `profile` remain reachable.
- **402 on a hospital** — the subscription is expired, cancelled, or beyond grace. Billing and support remain reachable.
- **Images/uploads fail** — configure MinIO/S3 or switch `FILESYSTEM_DISK` and `MEDIA_DISK` to `public`.
- **No demo tenants after setup** — `composer setup` does not seed; run `php artisan db:seed`.
- **Stale permissions** — run `php artisan permission:cache-reset` after changing roles/permissions in the database.
