<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Stancl\Tenancy\Database\Models\ImpersonationToken;
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
     */
    public function create(Request $request): Response
    {
        return Inertia::render('Auth/Login', [
            'isCentralDomain' => $this->isCentralDomain($request),
            'tenantName' => tenant()?->displayName(),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     *
     * Two flows share this endpoint:
     *  - `type=landlord`: authenticate against the platform database.
     *  - `type=tenant` + `subdomain`: authenticate against that hospital's
     *    database and hand the browser a one-time transfer token so the
     *    hospital session lives on its own subdomain.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'type' => 'required|in:landlord,tenant',
            'subdomain' => 'required_if:type,tenant|nullable|string|max:100',
        ], [
            'type.required' => 'Choose platform sign-in or hospital sign-in.',
            'type.in' => 'Choose platform sign-in or hospital sign-in.',
            'subdomain.required_if' => 'Enter your hospital subdomain.',
        ]);

        if ($validated['type'] === 'tenant') {
            return $this->authenticateTenant(
                $request,
                $validated['email'],
                $validated['password'],
                (string) $validated['subdomain'],
            );
        }

        return $this->authenticateLandlord($request, $validated['email'], $validated['password']);
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
     * Authenticate a hospital user and transfer the session to their domain.
     */
    private function authenticateTenant(Request $request, string $email, string $password, string $subdomain): RedirectResponse
    {
        $subdomain = strtolower(trim($subdomain));

        $tenant = Tenant::query()
            ->where('id', $subdomain)
            ->orWhereHas('domains', function ($query) use ($subdomain) {
                $query->where('domain', $subdomain)
                    ->orWhere('domain', 'like', $subdomain.'.%');
            })
            ->first();

        if ($tenant === null || ! $tenant->isActive()) {
            throw ValidationException::withMessages([
                'subdomain' => 'No active hospital matches that subdomain.',
            ]);
        }

        try {
            tenancy()->initialize($tenant);
        } catch (Throwable) {
            throw ValidationException::withMessages([
                'subdomain' => 'That hospital is not available right now.',
            ]);
        }

        try {
            if (! Auth::attempt(['email' => $email, 'password' => $password], false)) {
                throw ValidationException::withMessages([
                    'email' => __('auth.failed'),
                ]);
            }

            $user = Auth::user();

            $token = ImpersonationToken::create([
                'tenant_id' => $tenant->getTenantKey(),
                'user_id' => (string) $user->getKey(),
                'redirect_url' => url('/dashboard'),
                'auth_guard' => config('auth.defaults.guard'),
            ]);

            Auth::logout();
        } finally {
            if (tenancy()->initialized) {
                tenancy()->end();
            }
        }

        $request->session()->regenerate();

        $domain = $tenant->primaryDomain() ?? $tenant->getTenantKey();
        $port = $request->getPort();
        $portSuffix = in_array($port, [80, 443], true) ? '' : ':'.$port;

        return redirect()->away(sprintf(
            '%s://%s%s/login/transfer/%s',
            $request->getScheme(),
            $domain,
            $portSuffix,
            $token->token,
        ));
    }

    /**
     * Authenticate a platform (landlord) user.
     */
    private function authenticateLandlord(Request $request, string $email, string $password): RedirectResponse
    {
        if (! Auth::attempt(['email' => $email, 'password' => $password], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $user = Auth::user();

        if ($user->getAllPermissions()->pluck('name')->intersect(self::LANDLORD_PERMISSIONS)->isEmpty()) {
            Auth::guard('web')->logout();

            throw ValidationException::withMessages([
                'email' => 'This account belongs to a hospital. Sign in with your hospital subdomain instead.',
            ]);
        }

        $request->session()->regenerate();

        return $this->redirectByRole($user);
    }

    /**
     * Redirect the authenticated user to their role-appropriate dashboard.
     */
    private function redirectByRole($user): RedirectResponse
    {
        if ($user->hasRole('super-admin')) {
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
