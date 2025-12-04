<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Stancl\Tenancy\Facades\Tenancy;

/**
 * Handles user login for both landlord and tenant accounts.
 */
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

        Log::info('Login attempt', ['email' => $this->email]);

        try {
            // 2. Retrieve user
            $user = User::where('email', $this->email)->first();

            // 3. Verify User exists and Password matches
            if (! $user || ! Hash::check($this->password, $user->password)) {
                // Introduce a slight delay to prevent timing attacks
                sleep(1);
                throw ValidationException::withMessages([
                    'email' => [__('The provided credentials do not match our records.')],
                ]);
            }

            // 4. Check for active status
            if (! $user->is_active) {
                Log::warning('Login failed – inactive account', ['email' => $this->email]);
                throw ValidationException::withMessages([
                    'email' => [__('Your account has been deactivated. Please contact the administrator.')]
                ]);
            }

            // 5. Initialize tenancy *before* login if user is a tenant
            // This ensures the session is created in the correct context if using single-db or scoped sessions
            if ($user->role !== 'landlord' && ! empty($user->tenant_id)) {
                try {
                    Tenancy::initialize($user->tenant_id);
                    Log::info('Tenant initialised', ['tenant_id' => $user->tenant_id]);
                } catch (\Exception $e) {
                    Log::error('Failed to initialize tenant', ['tenant_id' => $user->tenant_id, 'error' => $e->getMessage()]);
                    throw ValidationException::withMessages([
                        'email' => [__('Tenant configuration error. Please contact support.')]
                    ]);
                }
            }

            // 6. Log the user in
            Auth::login($user, $this->remember);

            // 7. Regenerate the session for security
            Session::regenerate();

            Log::info('Login successful', ['email' => $this->email, 'role' => $user->role]);

            // 8. Redirect
            return $this->redirectToDashboard($user);

        } catch (ValidationException $e) {
            // Livewire handles this automatically, no need to log the stack trace for validation errors
            throw $e;
        } catch (\Throwable $e) {
            // Log unexpected errors
            Log::error('Login exception', [
                'email' => $this->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw ValidationException::withMessages([
                'email' => [__('An unexpected error occurred. Please try again.')]
            ]);
        }
    }

    /* --------------------
       Helpers
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
