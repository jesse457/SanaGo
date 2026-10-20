<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\PlatformContext;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blocks the hospital application when the tenant's subscription no longer
 * grants access (expired, cancelled, beyond the grace period, or never
 * assigned).
 *
 * Billing, support and notification endpoints stay reachable so a hospital
 * can renew without losing access to the means of paying.
 */
class EnsureSubscriptionIsActive
{
    /**
     * @var array<int, string>
     */
    private array $allowedPaths = [
        'billing',
        'billing/*',
        'support',
        'support/*',
        'notifications',
        'notifications/*',
        'logout',
        'locale',
        'profile',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $tenant = tenant();

        if ($tenant === null) {
            return $next($request);
        }

        // Landlord impersonation sessions and platform staff bypass the gate.
        if ($request->session()->has('impersonated_by')) {
            return $next($request);
        }

        $context = PlatformContext::forTenant($tenant);

        if ($context['subscription'] === null) {
            // No subscription recorded at all: let the tenant keep working
            // until the platform assigns one, but never block silently.
            return $next($request);
        }

        if ($context['subscription']['grants_access']) {
            return $next($request);
        }

        if ($request->is(...$this->allowedPaths)) {
            return $next($request);
        }

        $message = sprintf(
            'Your subscription is %s. Renew it to keep using %s.',
            strtolower((string) $context['subscription']['status_label']),
            (string) PlatformContext::setting('platform_name', 'SanaGo'),
        );

        if ($request->expectsJson() && ! $request->header('X-Inertia')) {
            abort(402, $message);
        }

        return Inertia::render('Error', [
            'status' => 402,
            'message' => $message,
            'title' => 'Subscription required',
        ])->toResponse($request)->setStatusCode(402);
    }
}
