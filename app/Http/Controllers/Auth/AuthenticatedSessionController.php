<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Stancl\Tenancy\Features\UserImpersonation;
use Throwable;

class AuthenticatedSessionController extends Controller
{
    /**
     * Permissions that identify a platform (landlord) account.
     *
     * @var array<int, string>
     */
    private const LANDLORD_PERMISSIONS = [
        'view-super-admin',
        'manage-tenants',
        'manage-landlord-users',
        'manage-platform-settings',
    ];

    /**
     * Display the login view.
     *
     * One screen for everybody. The context comes from the host: a hospital
     * subdomain signs that hospital's staff in, the platform domain signs
     * SanaGo staff in. Nobody types a domain.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('Auth/Login', [
            'tenantName' => tenant()?->displayName(),
            'isCentralDomain' => $this->isCentralDomain($request),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     *
     * The active tenancy decides which database is authenticated against:
     *  - hospital subdomain → that hospital's own users table;
     *  - platform domain   → the landlord users table.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $remember = $request->boolean('remember');
        $tenant = tenant();

        if (! Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $user = Auth::user();

        // Deactivated staff must not get a session (previously they could).
        if (property_exists($user, 'is_active') || array_key_exists('is_active', $user->getAttributes())) {
            if ($user->is_active === false) {
                Auth::guard('web')->logout();

                throw ValidationException::withMessages([
                    'email' => 'This account has been deactivated. Contact your administrator.',
                ]);
            }
        }

        if ($tenant === null) {
            // A hospital account on the platform domain: there is no tenant to
            // sign it into, and its hospital address is the right door.
            $isLandlord = $user->getAllPermissions()
                ->pluck('name')
                ->intersect(self::LANDLORD_PERMISSIONS)
                ->isNotEmpty();

            if (! $isLandlord) {
                Auth::guard('web')->logout();

                throw ValidationException::withMessages([
                    'email' => 'This is a hospital account. Sign in from your hospital address instead.',
                ]);
            }
        }

        $request->session()->regenerate();

        return $this->redirectByRole($user, $tenant !== null);
    }

    /**
     * Accept a one-time login transfer token on the hospital subdomain.
     */
    public function transfer(Request $request, ?string $token = null): RedirectResponse
    {
        if ($token === null || tenancy()->tenant === null) {
            abort(403);
        }

        try {
            return UserImpersonation::makeResponse($token);
        } catch (Throwable) {
            abort(403);
        }
    }

    /**
     * Send a password reset link to the user.
     */
    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email'),
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }

    /**
     * Handle a password reset request.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password),
                ])->save();
            },
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', __($status));
        }

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Redirect the authenticated user to their role-appropriate dashboard.
     *
     * Hospital dashboards only exist on a hospital domain; a platform user
     * always lands on the landlord dashboard.
     */
    private function redirectByRole($user, bool $inTenant): RedirectResponse
    {
        if ($user->hasRole('super-admin') || ! $inTenant) {
            return redirect()->route('dashboard.super-admin');
        }

        if ($user->hasRole('admin')) {
            return redirect()->route('dashboard.admin');
        }

        return redirect()->route('dashboard');
    }

    private function isCentralDomain(Request $request): bool
    {
        return in_array($request->getHost(), config('tenancy.central_domains'), true);
    }
}
