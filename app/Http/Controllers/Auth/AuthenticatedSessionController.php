<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'isCentralDomain' => in_array(request()->getHost(), config('tenancy.central_domains'), true),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'type' => 'required|in:landlord,tenant',
            'subdomain' => 'required_if:type,tenant|string|max:255',
        ]);

        if ($validated['type'] === 'tenant') {
            return $this->authenticateTenant($request, $validated);
        }

        return $this->authenticateLandlord($request, $validated);
    }

    /**
     * Authenticate a landlord user against the central database.
     */
    private function authenticateLandlord(Request $request, array $validated): RedirectResponse
    {
        if (! Auth::attempt([
            'email' => $validated['email'],
            'password' => $validated['password'],
        ], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        return $this->redirectByRole(Auth::user());
    }

    /**
     * Authenticate a tenant (hospital staff) user against the tenant database.
     *
     * When on the central domain, this looks up the tenant, initializes tenancy,
     * authenticates, and redirects to the tenant subdomain with a transfer token.
     * When on a tenant subdomain, it authenticates directly.
     */
    private function authenticateTenant(Request $request, array $validated): RedirectResponse
    {
        $subdomain = $validated['subdomain'];

        // Look up the tenant in the central database
        $tenant = Tenant::where('id', $subdomain)->first();

        if (! $tenant) {
            throw ValidationException::withMessages([
                'subdomain' => __('Hospital not found. Please check your hospital code.'),
            ]);
        }

        if (! $tenant->isActive()) {
            throw ValidationException::withMessages([
                'subdomain' => __('This hospital account is currently suspended.'),
            ]);
        }

        // Initialize tenancy so auth queries the tenant's database
        tenancy()->initialize($tenant);

        if (! Auth::attempt([
            'email' => $validated['email'],
            'password' => $validated['password'],
        ], $request->boolean('remember'))) {
            tenancy()->end();

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $user = Auth::user();
        $userId = $user->id;
        $tenantId = $tenant->id;

        tenancy()->end();

        // Get the tenant's primary domain
        $domain = $tenant->domains()->first()?->domain;

        if (! $domain) {
            throw ValidationException::withMessages([
                'subdomain' => __('This hospital has no domain configured.'),
            ]);
        }

        // Check if the user is already on the correct tenant subdomain
        if ($request->getHost() === $domain) {
            // Already on the correct domain — just regenerate session and redirect
            $request->session()->regenerate();

            return $this->redirectByRole($user);
        }

        // Cross-domain login: create an encrypted transfer token
        $token = Crypt::encrypt([
            'user_id' => $userId,
            'tenant_id' => $tenantId,
            'expires_at' => now()->addMinute()->timestamp,
        ]);

        return redirect()->away(
            'http://'.$domain.'/login/transfer?token='.urlencode($token),
        );
    }

    /**
     * Handle the auto-login transfer from a central-domain tenant login.
     * Validates the transfer token and logs the user in on the tenant subdomain.
     */
    public function transfer(Request $request): RedirectResponse
    {
        $token = $request->query('token');

        if (! $token) {
            abort(403, __('Missing transfer token.'));
        }

        try {
            $payload = Crypt::decrypt($token);
        } catch (DecryptException) {
            abort(403, __('Invalid transfer token.'));
        }

        if (! isset($payload['user_id'], $payload['tenant_id'], $payload['expires_at'])) {
            abort(403, __('Invalid transfer token payload.'));
        }

        if ($payload['expires_at'] < now()->timestamp) {
            abort(403, __('Transfer token has expired. Please log in again.'));
        }

        // Tenancy should already be initialized by InitializeTenancyBySubdomainGraceful,
        // but ensure it is initialized for the correct tenant
        if (! tenancy()->initialized()) {
            $tenant = Tenant::find($payload['tenant_id']);

            if (! $tenant) {
                abort(403, __('Tenant not found.'));
            }

            tenancy()->initialize($tenant);
        }

        Auth::loginUsingId($payload['user_id']);

        $request->session()->regenerate();

        return $this->redirectByRole(Auth::user());
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

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
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

        if ($user->hasRole('doctor')) {
            return redirect()->route('dashboard.doctor');
        }

        if ($user->hasRole('reception')) {
            return redirect()->route('dashboard.reception');
        }

        if ($user->hasRole('lab_tech')) {
            return redirect()->route('dashboard.lab');
        }

        if ($user->hasRole('pharma')) {
            return redirect()->route('dashboard.pharma');
        }

        return redirect()->route('dashboard.admin');
    }
}
