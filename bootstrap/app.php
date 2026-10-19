<?php

use App\Http\Middleware\EnsureCentralDomain;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            SetLocale::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'central.domain' => EnsureCentralDomain::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        /*
         * Inertia visits must never receive a bare framework error page: render
         * the branded `Error` page instead so failures stay inside the app.
         */
        $exceptions->respond(function (Response $response): Response {
            $status = $response->getStatusCode();

            if (! in_array($status, [403, 404, 419, 429, 500, 503], true)) {
                return $response;
            }

            $request = request();

            /*
             * JSON / API consumers keep the framework response; browsers and
             * Inertia visits get the branded page (Inertia returns the full
             * HTML shell for a first load, and JSON for an XHR visit).
             */
            if ($request->expectsJson() && ! $request->header('X-Inertia')) {
                return $response;
            }

            return Inertia::render('Error', ['status' => $status])
                ->toResponse($request)
                ->setStatusCode($status);
        });
    })->create();
