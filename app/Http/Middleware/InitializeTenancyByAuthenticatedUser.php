<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;

class InitializeTenancyByAuthenticatedUser
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // If user is logged in and has a tenant assigned
        if ($user && $user->tenant_id) {
            $tenant = Tenant::find($user->tenant_id);

            if ($tenant) {
                tenancy()->initialize($tenant);
            }
        }

        return $next($request);
    }
}
