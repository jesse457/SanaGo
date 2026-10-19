<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DemoRequestController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\LandlordRoleController;
use App\Http\Controllers\LandlordUserController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\TenantController;
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

    // // Auto-login transfer for cross-domain tenant login
    // Route::get('/login/transfer', [AuthenticatedSessionController::class, 'transfer'])
    //     ->name('login.transfer');

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
    Route::get('/tenants/create', fn () => Inertia::render('Tenants/Create'))
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
    Route::get('/subscriptions/create', [SubscriptionController::class, 'create'])
        ->name('subscriptions.create')
        ->middleware('can:manage-subscriptions');
    Route::post('/subscriptions', [SubscriptionController::class, 'store'])
        ->name('subscriptions.store')
        ->middleware('can:manage-subscriptions');
    Route::get('/subscriptions/{subscription}', [SubscriptionController::class, 'show'])
        ->name('subscriptions.show')
        ->middleware('can:view-tenants');
    Route::get('/subscriptions/{subscription}/edit', [SubscriptionController::class, 'edit'])
        ->name('subscriptions.edit')
        ->middleware('can:manage-subscriptions');
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
    Route::get('/support-tickets/{supportTicket}', [SupportTicketController::class, 'show'])
        ->name('support-tickets.show')
        ->middleware('can:view-tickets');
    Route::get('/support-tickets/create', [SupportTicketController::class, 'create'])
        ->name('support-tickets.create')
        ->middleware('can:manage-tickets');
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

    // System analytics (requires at least view-system-health)
    Route::get('/analytics', fn () => Inertia::render('Settings/Analytics'))
        ->name('analytics')
        ->middleware('can:view-system-health');
});
