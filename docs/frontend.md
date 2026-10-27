# Frontend

The frontend is an Inertia.js 3 + React 19 + TypeScript application rendered from `resources/views/app.blade.php`. There is no client-side router: Laravel routes return `Inertia::render()` and React pages receive props.

## Entry point

- `resources/js/app.tsx`
    - Boots Inertia with `createInertiaApp`.
    - Sets the document title.
    - Optionally imports `@/echo` when `VITE_REVERB_APP_KEY` exists, keeping Pusher/Echo out of the main bundle otherwise.
- `resources/views/app.blade.php`
    - Loads the theme class from `localStorage`/system preference before paint.
    - Loads fonts through `@fonts`, Ziggy routes through `@routes`, and Vite assets.
- `resources/js/echo.ts`
    - Reverb/Echo setup. Without a key, `window.Echo` stays undefined and consumers poll.

## Pages and layouts

Pages live in `resources/js/pages`, one per Inertia component. A page imports and wraps itself in its layout:

```tsx
import AppLayout from '@/Layouts/AppLayout';

export default function Index() {
    return <AppLayout headerTitle="Patients">...</AppLayout>;
}
```

| Layout             | Used for                                                     |
| ------------------ | ------------------------------------------------------------ |
| `AppLayout`        | Hospital workspace (sidebar, topbar, notifications, toaster) |
| `SuperAdminLayout` | Platform console                                             |
| `PublicLayout`     | Marketing site                                               |
| `ContextLayout`    | Shared routes that pick the right shell based on context     |

Pages can keep module-specific components in `resources/js/pages/<Module>/components`. Components used by two or more pages in the same module belong there; cross-module components belong in `resources/js/Components`.

## UI kit

`resources/js/Components/ui/index.ts` is the API reference. Pages must compose from it rather than defining their own controls.

Available primitives include:

- `Button` (primary/accent/secondary/outline/ghost/danger; renders an Inertia link when `href` is passed)
- `Card`, `SectionCard`, `PageHeader`, `Toolbar`, `StatCard`, `KeyValue`
- `Table`, `TableHeader`, `TableRow`, `TableHeaderCell`, `TableCell`, `TableBody`
- `Badge`, `Alert`, `EmptyState`, `Skeleton*`, `Dialog`, `AlertModal`, `Tabs`, `SegmentedControl`
- `swal` — the imperative SweetAlert (`swal.confirm / toast / success / error / warning / info / validation`), SweetAlert2 driven by the design tokens
- `Input`, `Select`, `Textarea`, `Checkbox`, `Switch`, `Field`, `FieldSet`
- `DropdownMenu`, `ProgressBar`, `Avatar`, `Breadcrumbs`

App-level pieces in `resources/js/Components`:

- `NotificationHandler` — the single feedback channel: shared `flash` props become SweetAlert toasts (success/info/warning) or the SweetAlert modal (error), and any Inertia validation bag opens the validation alert
- `Pagination`, `StatusBadge`, `DetailItem`, `LocaleSwitcher`, `AnnouncementBanner` (not part of the `ui` barrel; import from their own paths)
- `FormInput` / `FormSelect` / `FormTextarea` — legacy form helpers; prefer the UI kit for new work

A success is a `swal.toast(...)` raised by `NotificationHandler` from the server flash — pages must not toast or banner it themselves, that is what produced double notifications.

## Breadcrumbs

Every screen reads `module › page › deeper step` (`Patient Management › Patient Directory › Amine Ibekwe`). The trail is **derived**, not authored: each layout derives it from the current URL plus its own `NAV_CONFIG` and publishes it through `BreadcrumbTrailProvider` (`resources/js/lib/breadcrumbs.ts`).

So a page normally passes nothing. Pass `breadcrumbs` only when the trail needs something the URL cannot know — a record name, an invoice number:

```tsx
<PageHeader
    title={patient.name}
    breadcrumbs={[{ label: 'Patient Directory', href: '/patients' }, { label: patient.name }]}
/>
```

An explicit trail is a *continuation*: the module crumb is prepended when missing, crumbs pointing at a nav page inherit that page's nav label, and a lone `[{ label: title }]` is ignored in favour of the derived `module › page` trail.

Never use `window.confirm`, `window.alert` or `window.prompt`. Ask with the sweet alert instead, and gate the destructive work on its answer:

```tsx
const confirmed = await swal.confirm({
    danger: true,
    title: t('page.wards.delete_ward'),
    message: t('page.wards.delete_confirm', { name: ward.name }),
    confirmLabel: t('common.delete'),
    cancelLabel: t('common.cancel'),
});

if (!confirmed) {
    return;
}
```

## Design tokens

Tokens are defined in `resources/css/app.css` in three layers:

1. `@theme` — raw scale (warm neutral ramp, teal accent, status families, fonts, radii)
2. `@theme inline` — semantic tokens bound to runtime CSS variables
3. `:root` / `.dark` — actual theme values

Use semantic tokens, not palette steps, and never add `dark:` variants:

- Surfaces: `bg-canvas`, `bg-surface`, `bg-surface-muted`, `bg-surface-inset`, `bg-surface-hover`
- Text: `text-ink`, `text-ink-muted`, `text-ink-subtle`, `text-ink-inverse`
- Borders: `border-line`, `border-line-strong`
- Accent: `bg-accent`, `text-accent-ink`, `bg-accent-soft`
- Status: `bg-positive-soft` / `text-positive-ink`, `bg-warning-soft` / `text-warning-ink`, `bg-critical-soft` / `text-critical-ink`
- Elevation: `shadow-surface`, `shadow-raised`, `shadow-float`
- Radii: `rounded-md` for controls, `rounded-lg` / `rounded-xl` for panels, `rounded-full` only for badges/dots
- Motion: 150–300ms with `ease-premium`; animate transform/opacity only

Typography:

- Inter for product UI (`--font-sans`)
- Instrument Serif for marketing display text only (`--font-editorial`, `.font-editorial`)
- `tabular-nums` for numeric data

No gradients. Dark mode is automatic through the `.dark` class on `<html>`.

## Routing helpers

Wayfinder generates typed helpers from Laravel routes and controllers:

- `resources/js/routes/...` — named-route helpers grouped by URI
- `resources/js/actions/...` — controller action helpers
- `resources/js/wayfinder/` — runtime

Generated files should not be hand-edited. The Vite plugin regenerates them while the dev server runs.

```tsx
import { index as patientsIndex } from '@/routes/patients';

<Button href={patientsIndex.url()}>Patients</Button>;
```

Existing pages also use Ziggy's global `route()`:

```tsx
router.delete(route('patients.destroy', patient.id));
```

Both patterns are present. Prefer the typed Wayfinder helper for new code; use `route()` when a route name is not represented in the generated tree or when matching nearby code.

After adding or renaming a route, run `bun run dev` (or `bun run build`) so Wayfinder regenerates.

## Shared Inertia props

`HandleInertiaRequests` shares:

- `name`, `locale`, `translations`
- `auth.user` with `roles` and `permissions`
- `tenant` — id, name, slug, subscription, limits, features, usage (null on central)
- `isLandlord` — `true` on the central domain
- `platform` — platform name, tagline, currency, locales, trial/grace days, support contacts, maintenance state
- `flash` — success, error, info, price_required
- `announcements`
- `certificateCounts`

Use these instead of fetching the same data again.

## Forms and actions

Inertia's `useForm` is the standard for forms:

```tsx
const form = useForm({ name: '' });

form.post('/patients', { preserveScroll: true });
```

For simple links/actions, `router.get/post/put/patch/delete` works. Use `<Form>` from `@inertiajs/react` where appropriate.

Validation errors arrive as `form.errors`. Flash messages and the validation bag are displayed by `NotificationHandler` in the layout, so pages do not need their own success banners or error toasts — that double-toasts, because the server already flashed. Forms should still pass `error={errors.field}` to `Field` so the message sits next to the control.

## i18n

- Translations are JSON files: `lang/en.json`, `lang/fr.json`.
- `useTranslation()` returns `{ t, locale, switchLocale }`.
- It works inside a `TranslationProvider` or directly from page props.
- Missing French keys fall back to English.
- `switchLocale()` posts to `/locale`.

## Formatting and utilities

- `resources/js/lib/format.ts` — `formatCurrency`, `formatCurrencyCompact`, `formatDate`, `formatDateTime`, `formatDateRelative`, `formatDaysRemaining`, `formatNumber`
- `resources/js/lib/utils.ts` — `cn()` (clsx + tailwind-merge)
- `resources/js/lib/icons.tsx` — icon component resolution helpers
- `resources/js/types` — shared domain and page prop types

Use these helpers rather than duplicating currency/date formats in pages.

## Notifications

`useNotifications()` currently manages lab-request notifications in local storage and listens on a private Echo channel when available. The layout also exposes the database notification feed via `/notifications` routes. Realtime is opt-in; without Reverb the UI polls.

## Adding a page

1. Add the route in `routes/tenant.php` (hospital) or the central group in `routes/web.php`.
2. Add a controller method returning `Inertia::render('Module/Page', [...])`.
3. Create `resources/js/pages/Module/Page.tsx`.
4. Wrap it in `AppLayout`, `SuperAdminLayout`, or `PublicLayout`.
5. Compose from `@/Components/ui`; put one-off subcomponents in `pages/Module/components`.
6. Use semantic tokens and existing formatting helpers.
7. Run `bun run types:check`, `bun run lint:check`, and `bun run format:check`.

## Build and checks

```bash
bun run dev          # Vite dev server (also regenerates Wayfinder)
bun run build        # production build
bun run types:check  # tsc --noEmit
bun run lint         # ESLint --fix
bun run lint:check   # ESLint check
bun run format       # Prettier write
bun run format:check # Prettier check
```

If the browser shows an old bundle after a deploy, run `bun run build` or restart `bun run dev`.
