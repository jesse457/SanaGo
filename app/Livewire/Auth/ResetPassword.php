<?php

namespace App\Livewire\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;


class ResetPassword extends Component
{
    public $token;

    public $email;

    public $password;

    public $password_confirmation;

    public function mount(Request $request, $token = null)
    {
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

        $status = Password::broker()->reset(
            [
                'token' => $this->token,
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
            ],
            function ($user, $password) {
                $user->forceFill([
                    'password' => \Illuminate\Support\Facades\Hash::make($password),
                ])->setRememberToken(\Illuminate\Support\Str::random(60));
                if (!$user->hasVerifiedEmail()) {
                    $user->markEmailAsVerified();
                }
                $user->save();

                event(new \Illuminate\Auth\Events\PasswordReset($user));
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            session()->flash('status', trans($status));

            return redirect()->route('login');
        }

        $this->addError('email', trans($status));
    }

    public function render()
    {
        return view('livewire.auth.reset-password');
    }
}
