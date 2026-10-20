<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blocks every request to a suspended or archived tenant.
 *
 * Suspension is a platform action, so it has to be enforced at the edge: the
 * `is_active` / `suspended_at` / `archived_at` columns are checked on each
 * request. The tenant's own administrators still reach the support page and
 * the billing page so they can pay or ask for help.
 */
class EnsureTenantIsActive
{
    /**
     * Paths that stay reachable while a tenant is suspended.
     *
     * @var array<int, string>
     */
    private array $allowedPaths = [
        'support',
        'support/*',
        'billing',
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

        if ($tenant->isActive()) {
            return $next($request);
        }

        if ($request->is(...$this->allowedPaths)) {
            return $next($request);
        }

        $reason = $tenant->archived_at !== null
            ? 'This hospital account has been archived. Contact SanaGo support.'
            : 'This hospital account has been suspended. Contact SanaGo support.';

        if ($request->expectsJson() && ! $request->header('X-Inertia')) {
            abort(403, $reason);
        }

        return Inertia::render('Error', [
            'status' => 403,
            'message' => $reason,
            'title' => 'Account unavailable',
        ])->toResponse($request)->setStatusCode(403);
    }
}
