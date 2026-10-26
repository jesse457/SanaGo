<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keeps the public marketing pages on the central domain only.
 *
 * Hospital subdomains are the product itself: showing them the sales site
 * would make the hospital address look like the SanaGo homepage. Visitors
 * who land on a tenant host are sent into the app, where the auth middleware
 * forwards guests to that hospital's sign-in screen.
 */
class RedirectTenantsFromMarketing
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! in_array($request->getHost(), config('tenancy.central_domains'), true)) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
