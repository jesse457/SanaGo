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

    public string $test = '';

    public bool $remember = false;

    /* --------------------
       Validation rules
       -------------------- */
    protected array $rules = [
        'email' => ['required', 'string', 'email'],
        'password' => ['required', 'string', 'min:8'],
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
        // 1. Validate once and only once
        $this->validate($this->rules);

        Log::info('Login attempt', ['email' => $this->email]);

        try {
            // 2. Safely retrieve user by email for custom pre-login checks
            $user = User::where('email', $this->email)->first();

            // 3. Combined check for user existence and password correctness
            if (! $user || ! Hash::check($this->password, $user->password)) {
                // Throw a generic error for security, covering both user not found AND incorrect password
                throw ValidationException::withMessages([
                    'email' => ['We could not find an account with that email address or the provided password was incorrect.'],
                ]);
            }

            // 4. Check for active status
            if (! $user->is_active) {
                Log::warning('Login failed – inactive account', ['email' => $this->email]);
                throw ValidationException::withMessages(['email' => __('Your account has deactivated contact the admin for more info.')]);
            }

            // 5. Log the user in directly (since the password is now verified)
            Auth::login($user, $this->remember);

            // 6. Initialize tenancy *only if* the user is a tenant.
            if ($user->role !== 'landlord' && ! empty($user->tenant_id)) {
                Tenancy::initialize($user->tenant_id);
                Log::info('Tenant initialised', ['tenant_id' => $user->tenant_id]);
            }

            // 7. Regenerate the session for security.
            Session::regenerate();
            Log::info('Login successful', ['email' => $this->email, 'role' => $user->role]);

            // 8. Redirect the user to their appropriate dashboard.
            return $this->redirectToDashboard($user);
        } catch (ValidationException $e) {
            // Re-throw so Livewire can show the message on the form.
            throw $e;
            Log::error('Login exception', [
                'email' => $this->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Login exception', [
                'email' => $this->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw ValidationException::withMessages(['email' => __('An unexpected error occurred. Please try again.')]);
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

        // This ensures the redirect is detected by Livewire and the Pest tests
        return $this->redirect($route, navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
