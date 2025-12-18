<?php

use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // 1. FIX: Define the 'universal' group (Empty array is fine)
        // This stops the "Target class [universal] does not exist" error.
        $middleware->group('universal', []);
        $middleware->validateCsrfTokens(except: [
            'mcp/*',
        ]);
        // 2. Configure Web Group
        $middleware->web(append: [
            // Do NOT put 'universal' here.
            // Do NOT put 'InitializeTenancyByDomain' here if you have a Landing Page.
            SetLocale::class,
        ]);

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        // 3. THIS IS THE FIX: Custom Redirection for Guests
        $middleware->redirectGuestsTo(function () {
            // Check if we are currently in a Tenant context
            if (function_exists('tenant') && tenant()) {
                return route('tenant.login');
            }

            // Default to central login
            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
