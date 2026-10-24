# SanaGo documentation

These documents explain how the repository is put together and how to work in it. Start with the [README](../README.md) for the quick start, then use this index to go deeper.

| Document                                  | Read it when you need to...                                                             |
| ----------------------------------------- | --------------------------------------------------------------------------------------- |
| [Getting started](getting-started.md)     | Set up the app, seed demo data, sign in, and run the dev stack                          |
| [Project structure](project-structure.md) | Find where a file belongs or understand the directory layout                            |
| [Architecture](architecture.md)           | Understand request flow, the service layer, auth, billing, plans, and platform concerns |
| [Multi-tenancy](tenancy.md)               | Work on tenant provisioning, domains, suspension, impersonation, or tenant-scoped code  |
| [Frontend](frontend.md)                   | Build Inertia pages, use the UI kit, design tokens, and generated route helpers         |
| [Testing](testing.md)                     | Write or run Pest tests, especially tenant-aware tests                                  |

## The short version

SanaGo is one Laravel codebase with two contexts:

- **Central (landlord):** platform users manage tenants, plans, subscriptions, billing, support, and announcements. Data lives in `database/database.sqlite` (dev).
- **Tenant (hospital):** hospital staff use the application on their subdomain. Each tenant has an isolated SQLite database at `database/tenants/{tenant}/database.sqlite` (dev).

Routes are split accordingly:

- `routes/web.php` contains shared/universal routes, public marketing pages, and central-only platform routes.
- `routes/tenant.php` contains the hospital workspace and is only reachable on tenant subdomains.

Backend code follows a thin-controller/service architecture. Frontend code is Inertia + React, composed from a shared UI kit in `resources/js/Components/ui`.
