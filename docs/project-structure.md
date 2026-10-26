# Project structure

## Top level

```text
app/                    Laravel application code
bootstrap/              Framework bootstrap and middleware registration
config/                 Application, tenancy, platform, billing, media config
database/               Central + tenant migrations, seeders, SQLite files
docs/                   Developer documentation
lang/                   English/French validation and UI translations
public/                 Web root and built assets
resources/              CSS, Blade shell, React application
routes/                 web.php, tenant.php, console.php, channels.php
storage/                Logs, framework cache, tenant files
tests/                  Pest feature and unit tests
```

## Backend (`app/`)

```text
Console/Commands/       subscriptions:renew, subscriptions:expire,
                        subscriptions:notify-expiring, platform:health,
                        services sync command
Enums/                  Statuses and domain enums (InvoiceStatus, PaymentMethod,
                        PlanFeature, SubscriptionStatus, ...)
Http/
  Controllers/          Thin controllers grouped by area
  Middleware/           Tenant/subscription/plan enforcement, locale, Inertia
  Requests/             Validation for store/update operations
  Resources/            API resources where used
Models/                 Eloquent models (central and tenant)
Notifications/          Tenant lifecycle and platform notifications
Observers/              Billing and notification side effects
Policies/               Consultation, doctor pricing, general pricing policies
Providers/              AppServiceProvider, TenancyServiceProvider
Services/               Business logic, grouped by domain
Support/                PlatformContext, TenantDatabase, ErrorReport
Tenancy/                Custom tenant database manager
```

### Services

Business logic belongs here. Controllers should not contain multi-step workflows or direct financial calculations.

| Domain               | Examples                                                                                                    |
| -------------------- | ----------------------------------------------------------------------------------------------------------- |
| `Billing/`           | `BillingService`, `ConsultationPricingService`, `PriceListService`, `SupplyService`, `ClaimService`         |
| `Clinical/`          | `MedicalRecordService`, `NurseCareService`, `VitalService`                                                  |
| `Facility/`          | `FacilityService`                                                                                           |
| `Laboratory/`        | `LabRequestService`, `LabResultService`                                                                     |
| `PatientManagement/` | `AdmissionService`, `AppointmentService`, `PatientService`                                                  |
| `Pharmacy/`          | `InventoryService`, `PharmacyService`, `PrescriptionService`                                                |
| `Platform/`          | `TenantService`, `SubscriptionService`, `PlanLimitService`, `PlatformAuditService`, platform billing/health |
| `Radiology/`         | `RadiologyRequestService`, `RadiologyResultService`                                                         |
| `System/`            | `CommunicationService`, `NotificationService`, `SuperAdminDashboardService`, demo helpers                   |

### Central vs tenant models

A model either belongs to the central database or is tenant-scoped.

Central models explicitly set `protected $connection = 'central'`:

- `Tenant`, `Domain` (package model), `Plan`, `Subscription`, `SubscriptionInvoice`
- `PlatformSetting`, `PlatformAuditLog`, `Announcement`
- `DemoRequest`, `SupportTicket`, `TicketMessage`, `Note`

Tenant models use the default connection, which `stancl/tenancy` swaps when a tenant is active:

- `Patient`, `Appointment`, `Admission`, `MedicalRecord`, `Vital`
- `Prescription`, `Dispensation`, `Medication`, `Batch`, `Supply`
- `LabRequest`, `LabResult`, `LabTestDefinition`
- `RadiologyRequest`, `RadiologyResult`
- `Invoice`, `InvoiceItem`, `CashPayment`, `CashShift`, `Expenditure`
- `Payer`, `PatientInsurance`, `Claim`, `Service`, `DoctorServicePrice`
- `User`, `UserShift`, `Department`, `Ward`, `Bed`, `BedType`
- `Surgery`, `EmergencyCase`, `Certificate`, `Feedback`

`User` exists in both databases. The active connection decides which users table is queried.

### Models with side effects

Observers create financial and notification side effects:

- `MedicalRecordObserver` — attaches the consultation charge (honouring any negotiated fee stored on the matching appointment) and notifies staff. The appointment module stays decoupled from billing.
- Other observers cover admissions and related workflows.

If a new action should create a charge or notification, check whether it belongs in an observer or an explicit service call.

### Middleware

Registered aliases in `bootstrap/app.php`:

| Alias                 | Class                        | Purpose                                                              |
| --------------------- | ---------------------------- | -------------------------------------------------------------------- |
| `central.domain`      | `EnsureCentralDomain`        | Blocks platform routes on tenant hosts                               |
| `tenant.active`       | `EnsureTenantIsActive`       | Blocks suspended/archived tenants, with support/billing escape paths |
| `subscription.active` | `EnsureSubscriptionIsActive` | Blocks expired/cancelled subscriptions beyond grace                  |
| `feature`             | `EnsurePlanFeature`          | Requires a plan capability (`feature:radiology`)                     |
| `plan.limit`          | `EnforcePlanLimit`           | Enforces quotas on writes (`plan.limit:users`)                       |

`HandleInertiaRequests` and `SetLocale` are appended to the web group. `universal` is a marker group used by the tenancy package so shared routes keep working on both central and tenant hosts.

## Frontend (`resources/js/`)

```text
app.tsx                 Inertia entry point; optional Echo bootstrap
echo.ts                 Reverb/Echo setup and helpers
pages/                  Inertia page components, one per route
Layouts/                AppLayout, SuperAdminLayout, PublicLayout, ContextLayout
Components/ui/          Shared UI kit (the only UI vocabulary)
Components/marketing/   Public marketing sections
Components/brand/       Logo assets
Components/             Cross-page app pieces (toaster, flash, pagination...)
hooks/                  useNotifications, useTranslation, useMarketing
lib/                    format helpers, icon helpers, cn()
types/                  Shared TypeScript types
routes/                 Wayfinder-generated route helpers (do not edit)
actions/                Wayfinder-generated controller helpers (do not edit)
wayfinder/              Wayfinder runtime (generated)
```

Page modules mirror the backend domains: `Patients`, `Appointments`, `Admissions` (under `Patients`), `Consultations`, `Prescriptions`, `LabTests`, `Radiology`, `Pharmacy`, `Drugs`, `Supply`, `Finance`, `Caisse`, `Billing`, `Settings`, `Users`, `Roles`, `Shifts`, `Dashboard`, `Tenants`, `Plans`, `Subscriptions`, `Platform`, `SupportTickets`, `DemoRequests`, `Announcements`, `LandlordUsers`, `LandlordRoles`, `Auth`, `Profile`, and marketing pages at the root.

Pages import their layout directly. Hospital workspace pages use `AppLayout`; platform console pages use `SuperAdminLayout`; public pages use `PublicLayout`.

## Database (`database/`)

```text
database.sqlite                 Central database (dev)
tenants/{id}/database.sqlite    Per-hospital database (dev)
migrations/                     Central schema
migrations/tenant/              Per-hospital schema
migrations/test/                Tenant tables for the in-memory test DB
seeders/                        Central + tenant seeders
factories/                      Model factories
```

Central migrations create users, tenants, domains, impersonation tokens, plans, subscriptions, subscription invoices, platform settings, audit logs, support tickets, announcements, demo requests, permissions, media, and central notifications.

Tenant migrations create the hospital schema: patients, appointments, admissions, wards/beds, clinical records, pharmacy, lab, radiology, finance, pricing, cash register, users/roles/shifts, notifications, and related tables.

Seeding order:

1. `DatabaseSeeder` runs landlord roles, landlord users, platform settings, tenants, subscriptions.
2. `TenantSeeder` inserts each tenant and domain, creates its database, runs tenant migrations, then calls `TenantDatabaseSeeder`.
3. `TenantDatabaseSeeder` runs roles/permissions, reference data, services, staff, facility, patients, clinical, pharmacy, lab, finance, feedback, and shifts.

## Tests (`tests/`)

```text
Feature/Auth/                   Login behavior
Feature/Http/Controllers/       Controller and page tests
Feature/Models/                 Model behavior
Feature/Platform/               Tenancy, plans, subscriptions, super-admin panel
Feature/Services/               Service-level tests
Feature/Notifications/          Notification behavior
Unit/                           Unit tests
Helpers/                        resolveCentralToDefault, migrateTenantTables
Pest.php                        Test case binding, shared helpers, tenancy cleanup
```

## Where should new code go?

| Change                   | Location                                                                          |
| ------------------------ | --------------------------------------------------------------------------------- |
| New hospital page        | `routes/tenant.php` + controller + `app/Services` + `resources/js/pages/<Module>` |
| New platform page        | `routes/web.php` central group + controller + `resources/js/pages/<Module>`       |
| Validation               | `app/Http/Requests`                                                               |
| Authorization            | `app/Policies` or `can:` route middleware                                         |
| Tenant table             | `database/migrations/tenant` + model + factory                                    |
| Central table            | `database/migrations` + model with `$connection = 'central'`                      |
| Demo data                | `database/seeders` (tenant seeders for hospital data)                             |
| Shared UI                | `resources/js/Components/ui` (export through `index.ts`)                          |
| Module-only UI           | `resources/js/pages/<Module>/components`                                          |
| Cross-page app component | `resources/js/Components`                                                         |
| Test                     | `tests/Feature` (prefer) or `tests/Unit`                                          |
