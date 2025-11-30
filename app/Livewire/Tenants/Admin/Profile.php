<?php

namespace App\Livewire\Tenants\Admin;

use App\Traits\UserActivitiesTrait;
use Illuminate\Contracts\View\View; // Used for type-hinting the render return value
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class Profile extends Component
{
    // Imports the trait containing the logActivity method for recording user actions.
    use UserActivitiesTrait;

    // --- Profile Form Properties ---

    /**
     * The user's full name.
     */
    public string $name = '';

    /**
     * The user's email address.
     */
    public string $email = '';

    /**
     * The user's phone number (nullable).
     */
    public ?string $phone_number = null;

    /**
     * The user's physical address (nullable).
     */
    public ?string $address = null;

    // --- Password Form Properties ---

    /**
     * The current password, required for validation before changing.
     */
    public ?string $current_password = null;

    /**
     * The new password the user wishes to set.
     */
    public ?string $new_password = null;

    /**
     * Confirmation field for the new password.
     */
    public ?string $new_password_confirmation = null;

    // --- Lifecycle Hooks ---

    /**
     * Runs once immediately after the component is instantiated.
     * Initializes form properties with the currently authenticated user's data.
     *
     * @return void
     */
    public function mount(): void
    {
        // Fetch the currently authenticated user instance
        $user = Auth::user();

        // Populate Livewire properties from the user model
        $this->name = $user->name;
        $this->email = $user->email;
        // Use null-coalescing to ensure properties that might be null in the DB
        // are correctly typed as ?string (if not already handled by Eloquent casting)
        $this->phone_number = $user->phone_number;
        $this->address = $user->address;
    }

    // --- Profile Update Method ---

    /**
     * Validates and updates the user's name, email, phone number, and address.
     *
     * @return void
     */
    public function updateProfile(): void
    {
        // 1. Validate the input fields
        $this->validate([
            'name' => 'required|string|max:255',
            // Unique email check, ignoring the current user's ID
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        // Get the current user instance
        $user = Auth::user();

        // 2. Update the user record in the database
        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'address' => $this->address,
        ]);

        // 3. Log the activity using the trait method
        $this->logActivity(
            'profile_update',
            "User {$user->name} updated profile",
            [
                'user_id' => Auth::id(),
            ]
        );

        // 4. Show success notification
        LivewireAlert::title('Success')
            ->success()
            ->text('Profile updated successfully!')
            ->show();
    }

    // --- Password Update Method ---

    /**
     * Validates the current password and updates the user's password.
     *
     * @return void
     */
    public function updatePassword(): void
    {
        // 1. Validate the password fields
        $this->validate([
            'current_password' => 'required',
            // Password must be at least 8 chars and match the confirmation field
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // Get the current user instance
        $user = Auth::user();

        // 2. Check if the current password is correct using Hash facade
        if (!Hash::check((string) $this->current_password, $user->password)) {
            // Add a specific error to the 'current_password' field and stop execution
            $this->addError('current_password', 'Your current password is incorrect.');
            return;
        }

        // 3. Update the user's password (hashing the new one)
        $user->update([
            'password' => Hash::make((string) $this->new_password),
        ]);

        // 4. Log the activity
        $this->logActivity(
            'password_update',
            "User {$user->name} updated password",
            [
                'user_id' => Auth::id(),
            ]
        );

        // 5. Reset only the password fields to clear the form
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        // 6. Show success notification
        LivewireAlert::title('Success')
            ->success()
            ->text('Password updated successfully!')
            ->show();
    }

    // --- Render Method ---

    /**
     * Renders the view for the profile page.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render(): View
    {
        return \view('livewire.tenants.admin.profile');
    }
}
