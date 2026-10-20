<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\PlanFeature;
use App\Support\PlatformContext;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gates a route behind a plan capability.
 *
 *   Route::middleware('feature:radiology')
 *
 * Permissions answer "may this user?"; plan features answer "did this
 * hospital pay for it?". Both must pass, so this sits next to `can:` checks.
 */
class EnsurePlanFeature
{
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $tenant = tenant();

        if ($tenant === null) {
            return $next($request);
        }

        if (PlatformContext::featureEnabled($tenant, $feature)) {
            return $next($request);
        }

        $label = PlanFeature::tryFrom($feature)?->label() ?? ucfirst(str_replace('_', ' ', $feature));
        $message = sprintf('%s is not included in your current plan.', $label);

        if ($request->expectsJson() && ! $request->header('X-Inertia')) {
            abort(403, $message);
        }

        return Inertia::render('Error', [
            'status' => 403,
            'message' => $message,
            'title' => 'Upgrade required',
            'feature' => $feature,
        ])->toResponse($request)->setStatusCode(403);
    }
}
