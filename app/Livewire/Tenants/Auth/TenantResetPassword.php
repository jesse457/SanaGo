<?php

namespace App\Livewire\Tenants\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Component;

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
        $this->validate();

        // Because your User model uses 'BelongsToTenant',
        // Password::broker() will automatically respect the tenant scope.
        // It will fail if the email exists in the DB but belongs to a DIFFERENT tenant.
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

        if ($status == Password::PASSWORD_RESET) {
            session()->flash('status', trans($status));

            // Redirect to the TENANT login route
            return redirect()->route('tenant.login');
        }

        $this->addError('email', trans($status));
    }

    public function render()
    {
        return view('livewire.tenants.auth.reset-password');
    }
}
