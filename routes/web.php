<?php

use App\Livewire\About;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Blog;
use App\Livewire\BookDemo;
use App\Livewire\Career;
use App\Livewire\Docs;
use App\Livewire\Features;
use App\Livewire\Home as HomePage;
use App\Livewire\LandLord\CreateTenant;
use App\Livewire\LandLord\Dashboard;
use App\Livewire\LandLord\Feedback;
use App\Livewire\LandLord\ManageDemo;
use App\Livewire\LandLord\ManageSubscription;
use App\Livewire\LandLord\ManageTenants;
use App\Livewire\LandLord\RespondFeedback;
use App\Livewire\LandLord\Settings;
use App\Livewire\LandLord\ViewDemoRequest;
use App\Livewire\Pricing;
use App\Livewire\SendFeedback;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

// Health check endpoint
Route::get('/api/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now()->toIso8601String(),
    ]);
});

/*
|--------------------------------------------------------------------------
| Central Routes
|--------------------------------------------------------------------------
| We do NOT loop through domains here to avoid "Duplicate Route Name" errors.
| Instead, we define the routes once.
*/

Route::middleware(['web', 'universal'])
    ->domain(config('tenancy.central_domains.1'))
    ->group(function () {

        // --- PUBLIC PAGES ---
        Route::get('/', HomePage::class)->name('home');
        Route::get('/docs', Docs::class)->name('docs');
        Route::get('/about', About::class)->name('about');
        Route::get('/career', Career::class)->name('career');
        Route::get('/blog', Blog::class)->name('blog');
        Route::get('/pricing', Pricing::class)->name('pricing');
        Route::get('/features', Features::class)->name('features');
        Route::get('/book-demo', BookDemo::class)->name('book-demo');

        // --- AUTHENTICATION ---
        Route::get('/login', Login::class)->name('login');
        Route::get('/forgot-password', ForgotPassword::class)->name('password.request');
        Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');

        // --- UTILITIES ---
        Route::get('/language/{locale}', function (string $locale) {
            if (! in_array($locale, ['en', 'es', 'fr'])) {
                abort(400);
            }
            Session::put('locale', $locale);

            return redirect()->back();
        })->name('language.switch');

        // --- LANDLORD DASHBOARD ---
        Route::middleware(['role:landlord', 'auth'])
            ->name('landlord.') // Prefix route names
            ->group(function () {
                Route::get('/dashboard', Dashboard::class)->name('dashboard');
                Route::get('/tenants/{tenant}/subscription', ManageSubscription::class)->name('manage-subscription');
                Route::get('/settings', Settings::class)->name('settings');
                Route::get('/manage-tenants', ManageTenants::class)->name('manage-tenants');
                Route::get('/create-tenants', CreateTenant::class)->name('create-tenants');
                Route::get('/manage-demo', ManageDemo::class)->name('manage-demo');
                Route::get('/view-demo/{demoRequest}', ViewDemoRequest::class)->name('view-demo');

                Route::get('/feedbacks', Feedback::class)->name('feedbacks');
                Route::get('/respond-feedback/{feedback}', RespondFeedback::class)->name('respond-feedback');
                Route::get('/send-feedback', SendFeedback::class)->name('send-feedback');

                Route::post('/logout', function () {
                    Illuminate\Support\Facades\Auth::logout();
                    Session::invalidate();
                    Session::regenerateToken();

                    return redirect()->route('login');
                })->name('logout');
            });
    });
