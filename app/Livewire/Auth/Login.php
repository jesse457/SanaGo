<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Stancl\Tenancy\Facades\Tenancy;

/**
 * Handles user login for both landlord and tenant accounts.
 */
#[Layout('components.layouts.login')]
class Login extends Component
{
    /* --------------------
       Form fields
       -------------------- */
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    /* --------------------
       Validation rules
       -------------------- */
    protected array $rules = [
        'email' => ['required', 'string', 'email'],
        'password' => ['required', 'string'],
    ];

    /* --------------------
       Lifecycle
       -------------------- */
    public function mount()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard(Auth::user());
        }
    }

    /* --------------------
       Main authenticate flow
       -------------------- */
    public function authenticate()
    {
        // 1. Validate input
        $this->validate();

        // 2. Check Rate Limiter (Prevent Brute Force)
        $this->ensureIsNotRateLimited();

        Log::info('Login attempt', ['email' => $this->email]);

        try {
            // 3. Retrieve user
            $user = User::where('email', $this->email)->first();

            // 4. Verify User exists and Password matches
            if (! $user || ! Hash::check($this->password, $user->password)) {
                RateLimiter::hit($this->throttleKey()); // Increment failure count

                throw ValidationException::withMessages([
                    'email' => [__('The provided credentials do not match our records.')],
                ]);
            }

            // 5. Check for active status
            if (! $user->is_active) {
                Log::warning('Login failed – inactive account', ['email' => $this->email]);
                throw ValidationException::withMessages([
                    'email' => [__('Your account has been deactivated. Please contact the administrator.')],
                ]);
            }

            // 6. Initialize tenancy Logic
            if ($user->role === 'landlord') {
                // Landlords belong to the central application; no tenant init needed.
                Log::info('Landlord login proceeding', ['email' => $this->email]);
            } elseif (! empty($user->tenant_id)) {
                // Standard User: Must belong to a tenant
                try {
                    Tenancy::initialize($user->tenant_id);
                    Log::info('Tenant initialised', ['tenant_id' => $user->tenant_id]);
                } catch (\Exception $e) {
                    Log::error('Failed to initialize tenant', ['tenant_id' => $user->tenant_id, 'error' => $e->getMessage()]);
                    throw ValidationException::withMessages([
                        'email' => [__('Tenant configuration error. Please contact support.')],
                    ]);
                }
            } else {
                // User is not a landlord, but has no Tenant ID (Orphaned user)
                Log::warning('Login failed – user has no tenant assigned', ['email' => $this->email]);
                throw ValidationException::withMessages([
                    'email' => [__('You do not have permission to access this account.')],
                ]);
            }

            // 7. Log the user in
            Auth::login($user, $this->remember);

            // 8. Regenerate the session for security
            Session::regenerate();

            // Clear the rate limiter on success
            RateLimiter::clear($this->throttleKey());

            Log::info('Login successful', ['email' => $this->email, 'role' => $user->role]);

            // 9. Redirect
            return $this->redirectToDashboard($user);

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Login exception', [
                'email' => $this->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw ValidationException::withMessages([
                'email' => [__('An unexpected error occurred. Please try again.')],
            ]);
        }
    }

    /* --------------------
       Rate Limiting Helpers
       -------------------- */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }

    /* --------------------
       Navigation Helpers
       -------------------- */
    protected function redirectToDashboard(User $user)
    {
        $route = match ($user->role) {
            'landlord' => route('landlord.dashboard'),
            'admin' => route('admin.dashboard'),
            'doctor' => route('doctor.dashboard'),
            'nurse' => route('nurse.dashboard'),
            'lab-technician' => route('lab-technician.dashboard'),
            'pharmacist' => route('pharmacist.dashboard'),
            'receptionist' => route('receptionist.dashboard'),
            default => '/',
        };

        return $this->redirect($route, navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
