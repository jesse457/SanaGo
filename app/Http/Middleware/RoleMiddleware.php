<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // ---------------------------------------------------------------------
        // 1. Check Authentication
        // ---------------------------------------------------------------------
        if (! Auth::check()) {
            // API: Return 401 JSON
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            // Web: Redirect to appropriate login
            if (function_exists('tenant') && tenant()) {
                return redirect()->route('tenant.login');
            }

            return redirect()->route('login');
        }

        $user = Auth::user();

        // ---------------------------------------------------------------------
        // 2. Check Active Status
        // ---------------------------------------------------------------------
        if (! $user->is_active) {
            // API: Return 403 Forbidden JSON
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Your account has been deactivated.'], 403);
            }

            // Web: Logout and Redirect
            Auth::logout();
            $route = (function_exists('tenant') && tenant()) ? 'tenant.login' : 'login';

            return redirect()->route($route)
                ->with('error', 'Your account was deactivated.');
        }

        // ---------------------------------------------------------------------
        // 3. Check Role Permission
        // ---------------------------------------------------------------------
        if ($user->role !== $role) {
            // API: Return 403 Forbidden JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Access denied. You do not have the required role.',
                    'required_role' => $role,
                ], 403);
            }

            // Web: Redirect user to their OWN dashboard instead of a 403 page
            // This improves UX by "bouncing" them to the right place
            if (function_exists('tenant') && tenant()) {
                $dashboardRoute = 'dashboard'; // Default tenant dashboard
            } else {
                $dashboardRoute = 'landlord.dashboard'; // Default landlord dashboard
            }

            // Map roles to specific routes
            $redirectRoute = match ($user->role) {
                'admin' => 'admin.dashboard',
                'doctor' => 'doctor.dashboard',
                'nurse' => 'nurse.dashboard',
                'lab-technician' => 'lab-technician.dashboard',
                'pharmacist' => 'pharmacist.dashboard',
                'receptionist' => 'receptionist.dashboard',
                'landlord' => 'landlord.dashboard',
                default => $dashboardRoute,
            };

            return redirect()->route($redirectRoute)
                ->with('error', 'You do not have permission to access that page.');
        }

        return $next($request);
    }
}
