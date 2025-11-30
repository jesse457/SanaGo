<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Check Authentication & Redirect smartly
        if (! Auth::check()) {
            // Check if we are in a tenant context
            if (function_exists('tenant') && tenant()) {
                return redirect()->route('tenant.login');
            }
            
            // Otherwise, go to central login
            return redirect()->route('login');
        }

        $user = Auth::user();

        // 2. Check Active Status
        if (! $user->is_active) {
            Auth::logout();
            $route = (function_exists('tenant') && tenant()) ? 'tenant.login' : 'login';
            
            return redirect()->route($route)
                ->with('error', 'Your account was deactivated.');
        }

        // 3. Check Role Permission
        if ($user->role !== $role) {
            // Context-aware fallback dashboard
            if (function_exists('tenant') && tenant()) {
                // Use the generic tenant dashboard route
                $dashboardRoute = 'dashboard'; 
            } else {
                // Central Landlord fallback
                $dashboardRoute = 'landlord.dashboard';
            }

            // Or use your specific role match list:
            $redirectRoute = match ($user->role) {
                'admin'          => 'admin.dashboard',
                'doctor'         => 'doctor.dashboard',
                'nurse'          => 'nurse.dashboard',
                'lab-technician' => 'lab-technician.dashboard',
                'pharmacist'     => 'pharmacist.dashboard',
                'receptionist'   => 'receptionist.dashboard',
                'landlord'       => 'landlord.dashboard',
                default          => $dashboardRoute,
            };

            return redirect()->route($redirectRoute)
                ->with('error', 'You do not have permission to access that page.');
        }

        return $next($request);
    }
}