<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;


class ForgotPassword extends Component
{
    public $email = '';

    public $status = null;

    public $emailSent = false; // Controls the UI state

    protected $rules = [
        'email' => 'required|email',
    ];

    public function sendResetLink()
    {
        $this->validate();

        $response = Password::sendResetLink(['email' => $this->email]);

        if ($response == Password::RESET_LINK_SENT) {
            $this->status = trans($response);
            $this->emailSent = true; // Triggers the UI transition
        } else {
            $this->addError('email', trans($response));
        }
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
