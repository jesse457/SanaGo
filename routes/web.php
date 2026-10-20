<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DemoRequestController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\LandlordRoleController;
use App\Http\Controllers\LandlordUserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\PlatformAuditController;
use App\Http\Controllers\PlatformBillingController;
use App\Http\Controllers\PlatformHealthController;
use App\Http\Controllers\PlatformSettingsController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\TenantSubscriptionController;
use App\Http\Middleware\InitializeTenancyBySubdomainGraceful;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| 1. SHARED ROUTES (Central + Tenant)
|--------------------------------------------------------------------------
| InitializeTenancyBySubdomainGraceful initializes tenancy on subdomains
| but passes through on central domains (localhost, sanago.local).
| Auth routes work in both contexts:
|   - Central domain → central DB (super admins)
|   - Tenant subdomain → tenant DB (hospital staff)
*/
// Public marketing pages
Route::get('/', [LandingPageController::class, 'index'])->name('home');
Route::get('/features', [LandingPageController::class, 'features'])->name('features');
Route::get('/pricing', [LandingPageController::class, 'pricing'])->name('pricing');
Route::get('/about', [LandingPageController::class, 'about'])->name('about');
Route::get('/contact', [LandingPageController::class, 'contact'])->name('contact');
Route::post('/contact', [LandingPageController::class, 'submitDemoRequest'])->name('contact.submit');

Route::middleware([InitializeTenancyBySubdomainGraceful::class])->group(function () {
    // Auth
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:login');

    Route::get('/forgot-password', fn () => Inertia::render('Auth/ForgotPassword'))->name('password.request');
    Route::post('/forgot-password', [AuthenticatedSessionController::class, 'sendResetLink'])
        ->middleware('throttle:6,1')
        ->name('password.email');
    Route::get('/reset-password/{token}', fn (string $token) => Inertia::render('Auth/ResetPassword', ['token' => $token]))
        ->name('password.reset');
    Route::post('/reset-password', [AuthenticatedSessionController::class, 'resetPassword'])
        ->middleware('throttle:6,1')
        ->name('password.update');
    Route::get('/confirm-password', fn () => Inertia::render('Auth/ConfirmPassword'))->name('password.confirm');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    /*
     * Cross-domain login transfer: the platform login page authenticates a
     * hospital user against their own database and redirects here with a
     * one-time token so the session is created on the hospital subdomain.
     */
    Route::get('/login/transfer/{token?}', [AuthenticatedSessionController::class, 'transfer'])
        ->middleware('throttle:10,1')
        ->name('login.transfer');

    // Locale switcher (works for both guest and authenticated users)
    Route::post('/locale', function (Request $request) {
        $validated = $request->validate(['locale' => 'required|in:en,fr']);
        $locale = $validated['locale'];

        app()->setLocale($locale);
        session()->put('locale', $locale);

        if ($request->user() && Schema::hasColumn('users', 'locale')) {
            $request->user()->update(['locale' => $locale]);
        }

        return redirect()->back();
    })->name('locale.switch');

    // Shared authenticated routes (work in both central and tenant contexts)
    Route::middleware(['auth'])->group(function () {
        Route::get('/profile', fn () => Inertia::render('Profile/Show'))->name('profile');
        Route::get('/settings', fn () => Inertia::render('Settings/Index'))->name('settings');
    });
});

/*
|--------------------------------------------------------------------------
| 2. SUPER ADMIN ROUTES (Central Only)
|--------------------------------------------------------------------------
| Protected by can:view-super-admin — only users with this permission
| (from central DB) can access. Tenant users will get 403.
*/
Route::middleware(['auth', 'central.domain', 'web', 'throttle:api'])->group(function () {

    // Super admin dashboard – requires explicit super‑admin permission
    Route::get('/dashboard/super-admin', [TenantController::class, 'dashboard'])
        ->name('dashboard.super-admin')
        ->middleware('can:view-super-admin');

    // Tenant management
    Route::get('/tenants', [TenantController::class, 'index'])
        ->name('tenants.index')
        ->middleware('can:view-tenants');
    Route::get('/tenants/create', [TenantController::class, 'create'])
        ->name('tenants.create')
        ->middleware('can:manage-tenants');
    Route::post('/tenants', [TenantController::class, 'store'])
        ->name('tenants.store')
        ->middleware('can:manage-tenants');
    Route::get('/tenants/{tenant}', [TenantController::class, 'show'])
        ->name('tenants.show')
        ->middleware('can:view-tenants');
    Route::get('/tenants/{tenant}/edit', [TenantController::class, 'edit'])
        ->name('tenants.edit')
        ->middleware('can:manage-tenants');
    Route::put('/tenants/{tenant}', [TenantController::class, 'update'])
        ->name('tenants.update')
        ->middleware('can:manage-tenants');
    Route::delete('/tenants/{tenant}', [TenantController::class, 'destroy'])
        ->name('tenants.destroy')
        ->middleware('can:manage-tenants');
    Route::patch('/tenants/{tenant}/toggle-active', [TenantController::class, 'toggleActive'])
        ->name('tenants.toggle-active')
        ->middleware('can:manage-tenants');

    // Plan management
    Route::get('/plans', [PlanController::class, 'index'])
        ->name('plans.index')
        ->middleware('can:view-tenants'); // viewing plans requires tenant view permission
    Route::get('/plans/create', [PlanController::class, 'create'])
        ->name('plans.create')
        ->middleware('can:manage-plans');
    Route::post('/plans', [PlanController::class, 'store'])
        ->name('plans.store')
        ->middleware('can:manage-plans');
    Route::get('/plans/{plan}/edit', [PlanController::class, 'edit'])
        ->name('plans.edit')
        ->middleware('can:manage-plans');
    Route::put('/plans/{plan}', [PlanController::class, 'update'])
        ->name('plans.update')
        ->middleware('can:manage-plans');
    Route::delete('/plans/{plan}', [PlanController::class, 'destroy'])
        ->name('plans.destroy')
        ->middleware('can:manage-plans');

    // Subscription management
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])
        ->name('subscriptions.index')
        ->middleware('can:view-tenants');
    Route::get('/subscriptions/{subscription}', [SubscriptionController::class, 'show'])
        ->name('subscriptions.show')
        ->middleware('can:view-tenants');
    Route::put('/subscriptions/{subscription}', [SubscriptionController::class, 'update'])
        ->name('subscriptions.update')
        ->middleware('can:manage-subscriptions');
    Route::delete('/subscriptions/{subscription}', [SubscriptionController::class, 'destroy'])
        ->name('subscriptions.destroy')
        ->middleware('can:manage-subscriptions');

    // Demo requests (leads)
    Route::get('/demo-requests', [DemoRequestController::class, 'index'])
        ->name('demo-requests.index')
        ->middleware('can:view-leads');
    Route::get('/demo-requests/{demoRequest}', [DemoRequestController::class, 'show'])
        ->name('demo-requests.show')
        ->middleware('can:view-leads');
    Route::patch('/demo-requests/{demoRequest}/status', [DemoRequestController::class, 'updateStatus'])
        ->name('demo-requests.update-status')
        ->middleware('can:manage-leads');
    Route::post('/demo-requests/{demoRequest}/notes', [DemoRequestController::class, 'addNote'])
        ->name('demo-requests.add-note')
        ->middleware('can:manage-leads');

    // Support tickets
    Route::get('/support-tickets', [SupportTicketController::class, 'index'])
        ->name('support-tickets.index')
        ->middleware('can:view-tickets');
    // Static segments must be registered before the dynamic one, otherwise
    // /support-tickets/create is captured as {supportTicket} and 404s.
    Route::get('/support-tickets/create', [SupportTicketController::class, 'create'])
        ->name('support-tickets.create')
        ->middleware('can:manage-tickets');
    Route::get('/support-tickets/{supportTicket}', [SupportTicketController::class, 'show'])
        ->name('support-tickets.show')
        ->middleware('can:view-tickets');
    Route::post('/support-tickets', [SupportTicketController::class, 'store'])
        ->name('support-tickets.store')
        ->middleware('can:manage-tickets');
    Route::post('/support-tickets/{supportTicket}/messages', [SupportTicketController::class, 'addMessage'])
        ->name('support-tickets.add-message')
        ->middleware('can:manage-tickets');
    Route::patch('/support-tickets/{supportTicket}/status', [SupportTicketController::class, 'updateStatus'])
        ->name('support-tickets.update-status')
        ->middleware('can:manage-tickets');

    // Announcements
    Route::get('/announcements', [AnnouncementController::class, 'index'])
        ->name('announcements.index')
        ->middleware('can:view-tenants'); // or a dedicated 'view-announcements' if you create it
    Route::get('/announcements/create', [AnnouncementController::class, 'create'])
        ->name('announcements.create')
        ->middleware('can:manage-announcements');
    Route::post('/announcements', [AnnouncementController::class, 'store'])
        ->name('announcements.store')
        ->middleware('can:manage-announcements');
    Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update'])
        ->name('announcements.update')
        ->middleware('can:manage-announcements');
    Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])
        ->name('announcements.destroy')
        ->middleware('can:manage-announcements');

    // Landlord user management
    Route::get('/landlord-users', [LandlordUserController::class, 'index'])
        ->name('landlord-users.index')
        ->middleware('can:manage-landlord-users'); // index requires manage permission (or create a 'view-landlord-users')
    Route::get('/landlord-users/create', [LandlordUserController::class, 'create'])
        ->name('landlord-users.create')
        ->middleware('can:manage-landlord-users');
    Route::post('/landlord-users', [LandlordUserController::class, 'store'])
        ->name('landlord-users.store')
        ->middleware('can:manage-landlord-users');
    Route::get('/landlord-users/{user}/edit', [LandlordUserController::class, 'edit'])
        ->name('landlord-users.edit')
        ->middleware('can:manage-landlord-users');
    Route::put('/landlord-users/{user}', [LandlordUserController::class, 'update'])
        ->name('landlord-users.update')
        ->middleware('can:manage-landlord-users');
    Route::delete('/landlord-users/{user}', [LandlordUserController::class, 'destroy'])
        ->name('landlord-users.destroy')
        ->middleware('can:manage-landlord-users');

    // Landlord role & permission management
    Route::get('/landlord-roles', [LandlordRoleController::class, 'index'])
        ->name('landlord-roles.index')
        ->middleware('can:manage-landlord-users');
    Route::get('/landlord-roles/create', [LandlordRoleController::class, 'create'])
        ->name('landlord-roles.create')
        ->middleware('can:manage-landlord-users');
    Route::post('/landlord-roles', [LandlordRoleController::class, 'store'])
        ->name('landlord-roles.store')
        ->middleware('can:manage-landlord-users');
    Route::get('/landlord-roles/{role}/edit', [LandlordRoleController::class, 'edit'])
        ->name('landlord-roles.edit')
        ->middleware('can:manage-landlord-users');
    Route::put('/landlord-roles/{role}', [LandlordRoleController::class, 'update'])
        ->name('landlord-roles.update')
        ->middleware('can:manage-landlord-users');
    Route::delete('/landlord-roles/{role}', [LandlordRoleController::class, 'destroy'])
        ->name('landlord-roles.destroy')
        ->middleware('can:manage-landlord-users');

    /*
    |----------------------------------------------------------------------
    | Tenant lifecycle (suspend / reactivate / archive / restore / purge)
    |----------------------------------------------------------------------
    */
    Route::post('/tenants/{tenant}/suspend', [TenantController::class, 'suspend'])
        ->name('tenants.suspend')->middleware('can:manage-tenants');
    Route::post('/tenants/{tenant}/reactivate', [TenantController::class, 'reactivate'])
        ->name('tenants.reactivate')->middleware('can:manage-tenants');
    Route::post('/tenants/{tenant}/archive', [TenantController::class, 'archive'])
        ->name('tenants.archive')->middleware('can:manage-tenants');
    Route::post('/tenants/{tenant}/restore', [TenantController::class, 'restore'])
        ->name('tenants.restore')->middleware('can:manage-tenants');
    Route::post('/tenants/{tenant}/notes', [TenantController::class, 'addNote'])
        ->name('tenants.add-note')->middleware('can:manage-tenants');
    Route::post('/tenants/{tenant}/impersonate', [TenantController::class, 'impersonate'])
        ->name('tenants.impersonate')->middleware('can:impersonate-tenants');

    /*
    |----------------------------------------------------------------------
    | Per-tenant plan & subscription management
    |----------------------------------------------------------------------
    */
    Route::prefix('tenants/{tenant}/subscription')
        ->name('tenants.subscription.')
        ->middleware('can:manage-subscriptions')
        ->group(function () {
            Route::post('/', [TenantSubscriptionController::class, 'assign'])->name('assign');
            Route::post('/plan', [TenantSubscriptionController::class, 'changePlan'])->name('change-plan');
            Route::post('/renew', [TenantSubscriptionController::class, 'renew'])->name('renew');
            Route::post('/extend', [TenantSubscriptionController::class, 'extend'])->name('extend');
            Route::post('/cancel', [TenantSubscriptionController::class, 'cancel'])->name('cancel');
            Route::post('/resume', [TenantSubscriptionController::class, 'resume'])->name('resume');
            Route::post('/expire', [TenantSubscriptionController::class, 'expire'])->name('expire');
            Route::put('/overrides', [TenantSubscriptionController::class, 'overrides'])->name('overrides');
            Route::get('/preview', [TenantSubscriptionController::class, 'preview'])->name('preview');
            Route::post('/invoices/{invoice}/pay', [TenantSubscriptionController::class, 'payInvoice'])->name('pay-invoice');
        });

    /*
    |----------------------------------------------------------------------
    | Platform control: settings, billing, audit trail, health
    |----------------------------------------------------------------------
    */
    Route::get('/platform/settings', [PlatformSettingsController::class, 'edit'])
        ->name('platform.settings')->middleware('can:manage-platform-settings');
    Route::put('/platform/settings', [PlatformSettingsController::class, 'update'])
        ->name('platform.settings.update')->middleware('can:manage-platform-settings');

    Route::get('/platform/billing', [PlatformBillingController::class, 'index'])
        ->name('platform.billing')->middleware('can:view-billing');
    Route::get('/platform/audit', [PlatformAuditController::class, 'index'])
        ->name('platform.audit')->middleware('can:view-audit-logs');
    Route::get('/platform/health', [PlatformHealthController::class, 'index'])
        ->name('platform.health')->middleware('can:view-system-health');

    /*
    |----------------------------------------------------------------------
    | Platform notifications (the landlord bell)
    |----------------------------------------------------------------------
    | Landlord users receive subscription and tenant-lifecycle notifications
    | on the central notifications table; without these routes the bell had
    | nothing to read.
    */
    Route::get('/platform/notifications', [NotificationController::class, 'index'])
        ->name('landlord.notifications.index');
    Route::get('/platform/notifications/latest', [NotificationController::class, 'latest'])
        ->name('landlord.notifications.latest');
    Route::post('/platform/notifications/{id}/read', [NotificationController::class, 'markRead'])
        ->name('landlord.notifications.read');
    Route::post('/platform/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])
        ->name('landlord.notifications.mark-all-read');

    // System analytics (requires at least view-system-health)
    Route::get('/analytics', fn () => Inertia::render('Settings/Analytics'))
        ->name('analytics')
        ->middleware('can:view-system-health');
});
