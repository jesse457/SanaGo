<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;

/**
 * Like InitializeTenancyBySubdomain, but passes through on central domains
 * instead of throwing NotASubdomainException.
 */
class InitializeTenancyBySubdomainGraceful extends InitializeTenancyBySubdomain
{
    public function handle($request, Closure $next)
    {
        if (in_array($request->getHost(), config('tenancy.central_domains'), true)) {
            return $next($request);
        }

        return parent::handle($request, $next);
    }
}
