<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // These are safe as singletons ONLY if you don't use $this->state inside them
        $this->app->singleton(\App\Services\AppointmentService::class);
        $this->app->singleton(\App\Services\MedicalRecordService::class);
        $this->app->singleton(\App\Services\PharmacyService::class);
        $this->app->singleton(\App\Services\LabService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
