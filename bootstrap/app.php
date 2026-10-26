<?php

use App\Http\Middleware\EnforcePlanLimit;
use App\Http\Middleware\EnsureCentralDomain;
use App\Http\Middleware\EnsurePlanFeature;
use App\Http\Middleware\EnsureSubscriptionIsActive;
use App\Http\Middleware\EnsureTenantIsActive;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SetLocale;
use App\Support\ErrorReport;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            SetLocale::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        // Marker group consumed by stancl/tenancy's UniversalRoutes feature:
        // routes carrying it keep working on central domains even though the
        // tenancy identification middleware cannot resolve a subdomain there.
        $middleware->group('universal', []);

        $middleware->alias([
            'central.domain' => EnsureCentralDomain::class,
            'tenant.active' => EnsureTenantIsActive::class,
            'subscription.active' => EnsureSubscriptionIsActive::class,
            'feature' => EnsurePlanFeature::class,
            'plan.limit' => EnforcePlanLimit::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        /*
         * Inertia visits must never receive a bare framework error page: render
         * the branded `Error` page instead so failures stay inside the app.
         *
         * The page carries two faces. Production gets the calm copy only;
         * where `app.debug` is on it also gets the exception itself
         * (`debug`), because "something went wrong" is useless to the person
         * looking at a local 500 on the cash desk.
         */
        // $exceptions->respond(function (Response $response, Throwable $error, Request $request): Response {
        //     $status = $response->getStatusCode();

        //     if (! in_array($status, [403, 404, 419, 429, 500, 503], true)) {
        //         return $response;
        //     }

        //     /*
        //      * JSON / API consumers keep the framework response; browsers and
        //      * Inertia visits get the branded page (Inertia returns the full
        //      * HTML shell for a first load, and JSON for an XHR visit).
        //      */
        //     if ($request->expectsJson() && ! $request->header('X-Inertia')) {
        //         return $response;
        //     }

        //     return Inertia::render('Error', [
        //         'status' => $status,
        //         'debug' => config('app.debug') ? ErrorReport::collect($error, $request) : null,
        //     ])
        //         ->toResponse($request)
        //         ->setStatusCode($status);
        // });
    })->create();
