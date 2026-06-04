<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Blocks requests to landlord/super-admin routes that arrive from
 * tenant subdomains. Only central domains (localhost, sanago.local)
 * are allowed through.
 */
class EnsureCentralDomain
{
    public function handle(Request $request, Closure $next)
    {
        if (! in_array($request->getHost(), config('tenancy.central_domains'), true)) {
            abort(403);
        }

        return $next($request);
    }
}
