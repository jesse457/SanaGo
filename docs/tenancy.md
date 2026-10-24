# Multi-tenancy

SanaGo uses [`stancl/tenancy`](https://tenancyforlaravel.com/) with one database per hospital and a shared central database for platform data.

## Data layout (development)

```text
database/
  database.sqlite                    Central DB: tenants, domains, plans,
                                     subscriptions, platform users, support...
  tenants/
    city-hospital/database.sqlite    City General Hospital
    green-valley/database.sqlite     Green Valley Clinic
```

This nested layout is implemented by `App\Tenancy\DatabaseManager`, registered in `config/tenancy.php`. The package's built-in SQLite manager uses a flat path, so the custom manager is intentional: it creates, deletes, checks, and connects databases at `database/tenants/{tenant}/database.sqlite`.

## Configuration

`config/tenancy.php` defines:

- Tenant model: `App\Models\Tenant`
- Domain model: `Stancl\Tenancy\Database\Models\Domain`
- Central domains: `127.0.0.1`, `localhost`, `sanago.local`, `sanago.cm`
- Bootstrappers: database, cache, filesystem, queue
- SQLite manager: `App\Tenancy\DatabaseManager`
- Tenant migrations: `database/migrations/tenant`
- Tenant seeder: `Database\Seeders\TenantDatabaseSeeder`
- Features: `UserImpersonation`, `UniversalRoutes`, `CrossDomainRedirect`

`App\Providers\TenancyServiceProvider` wires the lifecycle events:

- `TenantCreated` → create database, migrate, seed (synchronous by default)
- `TenantDeleted` → delete database
- `TenancyInitialized` → bootstrap tenancy
- `TenancyEnded` → revert to central context
- Also sets the impersonation TTL from `config/platform.php` and enables the domain resolver cache.

## Domains and hosts

Seeded tenants store **bare subdomain fragments**:

```text
city-hospital
green-valley
```

A browser reaches them as:

```text
http://city-hospital.localhost:8000
http://green-valley.localhost:8000
```

`InitializeTenancyBySubdomain` extracts the first subdomain and looks it up in `domains`. In production, a tenant at `city-hospital.sanago.cm` works because the subdomain fragment is `city-hospital`.

### Creating tenants

When creating a tenant through the platform console or `TenantService::provision()`, use the **subdomain fragment** (`new-clinic`) for the domain, not the full host (`new-clinic.sanago.cm`). Route resolution is subdomain-fragment based.

> **Known limitation (current create form):** `resources/js/pages/Tenants/Create.tsx` pre-fills the domain as `${slug}.sanago.cm`. That full host is stored as-is, but `InitializeTenancyBySubdomain` looks up the extracted fragment (`new-clinic`) in `domains`, so a tenant created with the default value will not resolve on its subdomain. Replace the pre-filled value with the bare fragment until full-host resolution is implemented.

`TenantController::browserHostFor()` handles both forms when redirecting after impersonation:

- A stored full host is used as-is.
- A bare fragment is expanded against the current central host, or the first non-IP central domain, or `APP_URL`'s host.

## Route loading

Tenant routes are not registered in `bootstrap/app.php`. `TenancyServiceProvider::mapRoutes()` loads `routes/tenant.php` after the app boots.

`routes/tenant.php` applies:

```php
Route::middleware([
    'web',
    InitializeTenancyBySubdomain::class,
    PreventAccessFromCentralDomains::class,
    'throttle:api',
]);
```

The authenticated hospital workspace adds:

```php
Route::middleware(['auth', 'tenant.active', 'subscription.active']);
```

`routes/web.php` uses the package's `universal` marker group for shared auth/account routes so they work on both central and tenant hosts. Marketing pages are central-only; tenant hosts are redirected into the app.

## Provisioning

`App\Services\Platform\TenantService::provision()`:

1. Validates that the domain is unused.
2. Chooses the tenant seeder: `TenantDatabaseSeeder` when demo data is requested, otherwise `TenantProvisioningSeeder`.
3. Creates the tenant and domain inside a central transaction.
4. Creates the hospital administrator in the tenant database.
5. Assigns a subscription plan/trial.
6. Writes a platform audit entry and notifies landlord admins.

The `TenantCreated` pipeline creates the database, runs `database/migrations/tenant`, and seeds it. The default pipeline is synchronous; the comment in the provider notes that production may want it queued.

## Lifecycle

Tenant lifecycle is split between columns on `tenants` and subscription state.

| Action     | Effect                                                                                      |
| ---------- | ------------------------------------------------------------------------------------------- |
| Suspend    | `is_active = false`, `suspended_at` set. `EnsureTenantIsActive` returns 403 for app routes. |
| Reactivate | `is_active = true`, `suspended_at` cleared.                                                 |
| Archive    | `archived_at` set; data retained. Access blocked.                                           |
| Restore    | `archived_at` cleared.                                                                      |
| Purge      | Deletes the tenant and its database. Requires typing the tenant slug.                       |

Suspended/archived hospitals can still reach:

- `billing` / `billing/*`
- `support` / `support/*`
- `notifications` / `notifications/*`
- `logout`
- `locale`
- `profile`

Login remains available so a hospital administrator can reach billing or support. The platform console then shows the tenant as suspended/archived.

Subscription access is enforced separately by `EnsureSubscriptionIsActive`, which returns 402 outside the same escape paths. If a tenant has no subscription at all, access is allowed so provisioning can complete; assign a plan promptly.

## Support impersonation

1. A platform user with permission posts to `tenants.impersonate` from the central console.
2. `TenantController::impersonate()` picks the requested user or the tenant's first `admin`.
3. It verifies the tenant database exists via `TenantDatabase::exists()` and creates a one-time token through `tenancy()->impersonate()`.
4. The redirect goes to the tenant host's `/impersonate/{token}` endpoint.
5. `UserImpersonation::makeResponse()` consumes the token, logs the tenant user in, and redirects to `/dashboard`.
6. The central session records `impersonated_by`; `EnsureSubscriptionIsActive` bypasses subscription gating for such sessions.

Tokens expire after `config('platform.impersonation.ttl')` seconds (default 60). The token table lives in the central database (`tenant_user_impersonation_tokens`).

`TenantDatabase::exists()` intentionally resolves the package's database manager through `(new DatabaseConfig($tenant))->manager()`. The `TenantDatabaseManager` interface is not bound in the container; resolving it there silently returned `false` and broke impersonation.

## Cross-tenant code

Use `App\Support\TenantDatabase` for landlord-side reads/writes into a hospital database:

```php
use App\Support\TenantDatabase;

$count = TenantDatabase::run($tenant, fn () => Patient::count(), 0);
```

Why:

- It checks the database exists before initializing.
- It handles already-initialized tenancy.
- It always ends tenancy afterward.
- It catches and reports exceptions, returning a default instead of breaking the platform page.

Do not call `tenancy()->initialize()` directly in controllers without this guard.

## Adding tenant data

1. Create a migration in `database/migrations/tenant`.
2. Create the model (default connection) and factory.
3. Add relationships and casts.
4. Add demo data to the appropriate tenant seeder if useful.
5. Add/extend tests using `migrateTenantTables()`.

Do not put new hospital tables in `database/migrations` (central). The test helper and tenant pipeline will not see them.

## Testing tenancy

The test suite runs on in-memory SQLite. Full tenancy initialization swaps connections and does not survive `:memory:`, so tests use a lighter approach:

- `migrateTenantTables()` creates tenant tables from `database/migrations/test`.
- `actAsTenant($tenant)` sets the tenant on the tenancy container without swapping the DB.
- Tests disable `InitializeTenancyBySubdomain` and `PreventAccessFromCentralDomains` with `withoutMiddleware()`.
- `resolveCentralToDefault()` points the `central` connection at the default test connection.

See [Testing](testing.md) for examples.

## Gotchas

- **Bare domains vs full hosts** — store fragments for routing; impersonation expands fragments for redirects.
- **Tenant `name` accessor** — do not add one. The package hydrates `name` from the `data` JSON blob as a virtual column; an accessor breaks it. Use `displayName()`.
- **Central models** — always set `$connection = 'central'`.
- **Missing databases** — check `TenantDatabase::exists()` before initializing.
- **Domain resolver cache** — enabled in the provider. Model traits invalidate it on tenant/domain changes; direct DB writes bypass that invalidation.
- **Permissions cache** — tenant seeding resets it; after manual role changes run `php artisan permission:cache-reset`.
- **Route list** — tenant routes are loaded by the provider, not `bootstrap/app.php`; `php artisan route:list` still shows them once the provider boots.
