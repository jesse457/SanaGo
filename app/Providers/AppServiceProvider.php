<?php

namespace App\Providers;

use App\Models\MedicalRecord;
use App\Policies\ConsultationPolicy;
use App\Support\PlatformContext;
use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureGates();
        $this->configureRateLimiting();
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    protected function configureGates(): void
    {
        // The clinical model is `MedicalRecord`; the policy speaks the domain
        // language ("consultation") used by the billing flow.
        Gate::policy(MedicalRecord::class, ConsultationPolicy::class);

        Gate::before(function ($user) {
            /*
             * Only the platform super-admin role bypasses the permission
             * matrix, and only in the central (landlord) context.
             *
             * This used to return `true` for *any* central user holding
             * `view-super-admin`, which every landlord role has — so a
             * sales-rep could delete tenants despite the `can:manage-tenants`
             * route guards. The bypass is now tied to the role named in
             * config('platform.super_admin_role').
             *
             * hasRole() is used (not can()) to avoid gate recursion.
             */
            if (tenant('id') === null && $user->hasRole(config('platform.super_admin_role', 'super-admin'))) {
                return true;
            }

            return null;
        });
    }

    protected function configureRateLimiting(): void
    {
        // Login throttling: 5 attempts per minute per IP + email
        RateLimiter::for('login', function (Request $request) {
            $key = 'login:'.strtolower((string) $request->input('email')).'|'.$request->ip();

            return Limit::perMinute(5)->by($key);
        });

        /*
         * General API limiter. The limit follows the tenant's plan
         * (`plans.api_rate_limit`) so the column stops being decorative.
         * Central/landlord traffic keeps a fixed, higher ceiling.
         */
        RateLimiter::for('api', function (Request $request) {
            $tenant = tenant();
            $perMinute = 120;

            if ($tenant !== null) {
                try {
                    $perMinute = (int) (PlatformContext::forTenant($tenant)['plan']?->api_rate_limit ?? $perMinute);
                } catch (Throwable) {
                    $perMinute = 120;
                }
            }

            return Limit::perMinute(max(30, $perMinute))->by($request->ip());
        });
    }
}
