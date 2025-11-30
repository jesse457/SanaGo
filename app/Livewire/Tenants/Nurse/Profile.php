<?php

namespace App\Livewire\Tenants\Nurse;

use App\Traits\UserActivitiesTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;

use Livewire\Attributes\Layout;

#[Layout('components.layouts.nurse')]
class Profile extends Component
{
      use UserActivitiesTrait;
    // Editable profile fields
    public string $name;
    public string $email;
    public string $phone_number;
    public string $address;

    // Password fields
    public string $current_password;
    public string $new_password;
    public string $new_password_confirmation;

    public array $shifts = []; // Read-only shifts
    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone_number = $user->phone_number;
        $this->address = $user->address;

           $this->loadShifts();
    }

     protected function loadShifts()
    {
        // Load user's shifts ordered by date descending, then start_time
        $this->shifts = Auth::user()->shifts()
            ->orderBy('shift_date', 'desc')
            ->orderBy('start_time')
            ->get()
            ->toArray();
    }
    public function updateProfile()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'address' => $this->address,
        ]);
        $this->logActivity(
            'profile_update',
            "User {$user->name} updated profile",
            [
                'user_id' => Auth::id(),
            ]
        );
        LivewireAlert::title('Success')->success()->text('Profile updated successfully!')->show();
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Your current password is incorrect.');
            return;
        }

        $user->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->logActivity(
            'password_update',
            "User {$user->name} updated password",
            [
                'user_id' => Auth::id(),
            ]
        );
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        LivewireAlert::title('Success')->success()->text('Password updated successfully!')->show();
    }
    public function render()
    {
        return view('livewire.tenants.nurse.profile');
    }
}
