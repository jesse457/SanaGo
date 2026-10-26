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
 *
 * Additionally enforces the tenant's quota counters (users / patients /
 * storage) — a plan that "includes" a feature is useless if the account is
 * already over its limits.
 */
final class EnsurePlanFeature
{
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $tenant = tenant();

        if ($tenant === null) {
            return $next($request);
        }

        $context = PlatformContext::forTenant($tenant);

        if (! ($context['features'][$feature] ?? false)) {
            $label = PlanFeature::tryFrom($feature)?->label()
                ?? ucfirst(str_replace('_', ' ', $feature));

            return $this->upgrade(
                $request,
                sprintf('%s is not included in your current plan.', $label),
                $feature,
            );
        }

        return $next($request);
    }

    private function upgrade(Request $request, string $message, string $feature): Response
    {
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
