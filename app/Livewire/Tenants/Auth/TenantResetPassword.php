<?php

namespace App\Livewire\Tenants\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log; // Imported Log Facade
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.login')]
class TenantResetPassword extends Component
{
    public $token;

    public $email;

    public $password;

    public $password_confirmation;

    public function mount(Request $request, $token = null)
    {
        // Security Check
        if (! tenant()) {
            Log::warning('Password reset attempted without valid tenant context.', [
                'ip' => $request->ip(),
                'email' => $request->query('email')
            ]);
            abort(404);
        }

        $this->token = $token;
        $this->email = $request->query('email');
    }

    protected $rules = [
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|confirmed|min:8',
    ];

    public function resetPassword()
    {
        // 1. Log the attempt start
        Log::info('Tenant password reset attempt started.', [
            'tenant_id' => tenant()->id ?? 'unknown',
            'email' => $this->email,
            'ip' => request()->ip(),
        ]);

        $this->validate();

        try {
            // Because your User model uses 'BelongsToTenant',
            // Password::broker() will automatically respect the tenant scope.
            $status = Password::broker()->reset(
                [
                    'token' => $this->token,
                    'email' => $this->email,
                    'password' => $this->password,
                    'password_confirmation' => $this->password_confirmation,
                ],
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                    ])->setRememberToken(Str::random(60));

                    if (! $user->hasVerifiedEmail()) {
                        $user->markEmailAsVerified();
                    }

                    $user->save();

                    event(new PasswordReset($user));
                }
            );

            // 2. Check for Success
            if ($status == Password::PASSWORD_RESET) {
                Log::info('Tenant password reset successful.', [
                    'tenant_id' => tenant()->id ?? 'unknown',
                    'email' => $this->email,
                ]);

                session()->flash('status', trans($status));

                // Redirect to the TENANT login route
                return redirect()->route('tenant.login');
            }

            // 3. Log Logic Failure (e.g., Invalid Token, User not found)
            // We log the raw status string (e.g., passwords.token) and the translated message.
            Log::warning('Tenant password reset failed.', [
                'tenant_id' => tenant()->id ?? 'unknown',
                'email' => $this->email,
                'status_code' => $status,
                'reason' => trans($status),
            ]);

            $this->addError('email', trans($status));

        } catch (\Throwable $e) {
            // 4. Log System/Crash Errors (DB connection, Mailer issues, code bugs)
            Log::error('Tenant password reset system exception.', [
                'tenant_id' => tenant()->id ?? 'unknown',
                'email' => $this->email,
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(), // Full stack trace for deep debugging
            ]);

            $this->addError('email', 'An unexpected system error occurred. Please try again later.');
        }
    }

    public function render()
    {
        return view('livewire.tenants.auth.reset-password');
    }
}
