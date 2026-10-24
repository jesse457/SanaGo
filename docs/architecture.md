# Architecture

SanaGo is an Inertia monolith: Laravel handles routing, validation, authorization, and business logic, then renders React pages through Inertia. There is no separate API for the product UI.

## Two contexts, one codebase

|               | Central / landlord                                     | Tenant / hospital                                       |
| ------------- | ------------------------------------------------------ | ------------------------------------------------------- |
| Host          | `localhost`, `sanago.local`, `sanago.cm`               | `<hospital>.localhost`, `<hospital>.sanago.cm`          |
| Database      | `database/database.sqlite`                             | `database/tenants/{id}/database.sqlite`                 |
| Users         | SanaGo staff                                           | Hospital staff                                          |
| Routes        | Marketing, shared auth, platform console               | Hospital workspace                                      |
| Main concerns | Tenants, plans, subscriptions, billing, support, audit | Patients, clinical work, diagnostics, pharmacy, finance |

See [Multi-tenancy](tenancy.md) for the full mechanism.

## Request lifecycle

A hospital request:

1. **Host resolution** — `InitializeTenancyBySubdomain` maps the subdomain to a tenant. The tenancy bootstrappers swap the default database connection, cache prefix, filesystem root, and queue context.
2. **Platform gates** — `tenant.active` rejects suspended/archived tenants; `subscription.active` rejects lapsed subscriptions. Both allow billing, support, notifications, logout, locale, and profile so a hospital can recover access.
3. **Authentication** — the `auth` middleware resolves the user from the active database. Deactivated users are rejected at login.
4. **Authorization** — route `can:` middleware and policies check permissions. Role definitions differ between central and tenant contexts.
5. **Plan gates** — `feature:...` checks whether the hospital's plan includes a module; `plan.limit:...` checks quotas on writes.
6. **Controller** — validates through a form request and delegates to a service.
7. **Service** — performs the workflow, possibly inside a transaction, and fires model events.
8. **Model / observer** — persists data and applies side effects such as billing or notifications.
9. **Inertia** — the controller returns `Inertia::render('Module/Page', $props)`; React renders the page inside its layout.

A central request follows the same shape but stays on the central connection and uses the `central.domain` guard for platform routes.

## Backend layers

### Routes

- `routes/web.php`
    - Public marketing pages (central only; `RedirectTenantsFromMarketing` sends tenant hosts to their app).
    - Shared/universal auth and account routes (`login`, password reset, locale, profile, settings).
    - Central-only platform console protected by `auth`, `central.domain`, and permissions.
    - Tenant lifecycle endpoints under `/tenants`.
- `routes/tenant.php`
    - Loaded by `TenancyServiceProvider`.
    - Protected by `web`, `InitializeTenancyBySubdomain`, `PreventAccessFromCentralDomains`, and `throttle:api`.
    - Contains the impersonation token acceptance endpoint and the authenticated hospital workspace.

### Controllers

Controllers are thin. They validate input, call a service or query, and return an Inertia response or redirect. Business rules, multi-step writes, and financial calculations belong in services.

### Form requests

Validation lives in `app/Http/Requests`, paired as `Store...Request` / `Update...Request` per resource. Inline controller validation is avoided for resource writes.

### Services

`app/Services` is the business-logic layer, grouped by domain:

- `Billing` — invoices, charges, payments, pricing, claims, supplies
- `Clinical` — medical records, nurse care, vitals
- `Facility` — wards, beds, departments
- `Laboratory` — requests and results
- `PatientManagement` — patients, appointments, admissions
- `Pharmacy` — inventory, prescriptions, dispensing
- `Platform` — tenant lifecycle, subscriptions, plans/limits, billing, audit, health
- `Radiology` — requests and results
- `System` — notifications, communication, dashboards, demo helpers

### Policies and RBAC

Permissions use `spatie/laravel-permission`:

- **Central roles** are seeded by `SuperAdminRoleSeeder`: `super-admin`, `tenant-manager`, `billing-manager`, `support-agent`, `sales-rep`. Permissions cover tenant management, plans/subscriptions, billing, support, leads, announcements, platform settings, and audit.
- **Tenant roles** are seeded by `RoleSeeder`: `admin`, `reception`, `doctor`, `lab_tech`, and `pharma`. Permissions cover patients, appointments, admissions, consultations, vitals, lab, radiology, pharmacy, finance, caisse, users, roles, and audit logs.
- `Gate::before` grants the configured super-admin role a blanket pass, but only in the central context (`tenant()` is null). Other landlord roles stay bound by their permissions.
- Clinical authorization uses `ConsultationPolicy` mapped to `MedicalRecord`.

### Observers and billing

Some financial behavior is event-driven:

- `AppointmentObserver` creates a consultation charge when an appointment is booked, using the doctor's tariff.
- `MedicalRecordObserver` attaches the consultation charge when a record is created and notifies staff.
- `AdmissionObserver`, `PrescriptionObserver`, `LabRequestObserver`, `SurgeryObserver`, and `EmergencyCaseObserver` handle their own billing/notification side effects.
- `BillingService` owns invoice numbering, draft invoice reuse, billable items, payments, and totals.

When adding a new billable workflow, decide whether it belongs in an observer (automatic side effect) or an explicit service call (user-initiated action). Avoid duplicating pricing logic outside `Billing`.

### Plans, features, and quotas

The platform sells plans (`Plan`) with:

- Pricing (monthly/yearly, XAF)
- Limits: users, patients, storage, API rate limit
- Feature flags: appointments, laboratory, radiology, pharmacy, inventory, admissions, emergency, reports, cash register, insurance claims, audit logs

`PlatformContext` is a request-scoped read model for the active tenant's subscription, limits, features, and usage. It degrades gracefully when central tables are missing.

- `EnsurePlanFeature` turns missing features into a 403 "Upgrade required" page.
- `EnforcePlanLimit` checks write requests and flashes a clear quota error.
- `PlanLimitService` computes usage and limits, including per-tenant overrides.
- The API rate limiter reads `plans.api_rate_limit`.

Subscription status is separate from plan features. `Subscription` tracks `trialing`, `active`, `past_due`, `expired`, `cancelled`, and `inactive`; grace periods are date-driven. `EnsureSubscriptionIsActive` enforces access.

### Notifications

`NotificationService` and domain services write database notifications and, when enabled, broadcast through Reverb. Relevant toggles live in `config/platform.php` and platform settings:

- `PLATFORM_REALTIME_NOTIFICATIONS`
- `PLATFORM_MAIL_NOTIFICATIONS`
- notification expiry warning days

The frontend uses `useNotifications`; without Reverb it falls back to polling.

### Audit and media

- `spatie/laravel-activitylog` is enabled on key clinical models such as `Patient`, `Admission`, `MedicalRecord`, and `Prescription`.
- `PlatformAuditLog` records landlord actions such as tenant provisioning, suspension, and impersonation.
- `spatie/laravel-medialibrary` stores patient photos, staff avatars, lab/radiology reports, medical record attachments, and medication/supply images. Use `HasMedia` + `InteractsWithMedia`; never handle uploads manually.

### Errors

`bootstrap/app.php` renders a branded Inertia `Error` page for 403, 404, 419, 429, 500, and 503 responses. In debug mode the page includes an `ErrorReport` (exception, message, location, request, trace). JSON consumers keep the framework response.

### Scheduled work

`routes/console.php` schedules:

- `subscriptions:renew` — daily
- `subscriptions:expire` — daily
- `subscriptions:notify-expiring` — daily
- `platform:health` — every six hours

Production requires the Laravel scheduler cron entry.

## Design constraints

- Keep tenant data inside tenant databases. Cross-tenant reads go through `TenantDatabase::run()` or an explicit `tenancy()->initialize()` block.
- Use `TenantDatabase::exists()` before initializing tenancy for another tenant; initializing a missing database can poison the connection manager.
- Central models must set `$connection = 'central'`.
- Do not add a `name` accessor to `Tenant`; stancl/tenancy stores it as a virtual column in `data`.
- Use named routes and Wayfinder/Ziggy rather than hardcoded URLs.
