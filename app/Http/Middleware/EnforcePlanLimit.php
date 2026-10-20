<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\Platform\PlanLimitService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Enforces a plan quota on a write endpoint.
 *
 *   Route::middleware('plan.limit:users')
 *
 * Only mutating requests are checked so reading a form stays possible once a
 * quota is exhausted (the create page shows the upgrade prompt).
 */
class EnforcePlanLimit
{
    public function __construct(private readonly PlanLimitService $limits) {}

    public function handle(Request $request, Closure $next, string $key): Response
    {
        if ($request->isMethod('post') || $request->isMethod('put') || $request->isMethod('patch')) {
            $this->limits->ensureCan($key);
        }

        return $next($request);
    }
}
