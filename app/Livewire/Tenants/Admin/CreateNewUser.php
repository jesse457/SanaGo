<?php

namespace App\Livewire\Tenants\Admin;

use App\Mail\UserInvitationMail;
use App\Mail\UserWelcomeMail; // [FIX] Imported the new Mailable
use App\Models\Department;
use App\Models\User;
use App\Traits\UserActivitiesTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Password; // Required for token generation

#[Layout('components.layouts.admin')]
class CreateNewUser extends Component
{
    use UserActivitiesTrait, WithFileUploads;

    // --- Properties ---
    public $name;
    public $phone_number;
    public $address;
    public $gender;
    public $profile_picture;
    public $department_id;
    public $hire_date;
    public $role;
    public $is_active = true;
    public $email;
    public $generatedPassword;
    public $departments;

    protected function rules()
    {
        $tenantId = tenant('id');

        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->where('tenant_id', $tenantId)],
            'phone_number' => ['nullable', 'string', 'max:20', Rule::unique('users', 'phone_number')->where('tenant_id', $tenantId)],
            'address' => 'nullable|string|max:255',
            'gender' => ['nullable', Rule::in(['Male', 'Female', 'Other'])],
            'department_id' => ['nullable', Rule::exists('departments', 'id')->where('tenant_id', $tenantId)],
            'hire_date' => 'nullable|date',
            'role' => ['required', Rule::in(['admin', 'doctor', 'nurse', 'receptionist', 'lab-technician', 'pharmacist'])],
            'is_active' => 'boolean',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function mount()
    {
        // Load departments specific to this tenant
        $this->departments = Department::where('tenant_id', tenant('id'))->get(['id', 'name']);
        $this->generatePassword();
    }

    public function updated($propertyName)
    {
        // Real-time validation for file uploads
        if ($propertyName === 'profile_picture') {
            try {
                $this->validateOnly($propertyName);
            } catch (ValidationException $e) {
                // Remove the failed file from temporary storage to save space
                $this->profile_picture = null;
                throw $e;
            }
        }
    }

    protected function generatePassword()
    {
        $this->generatedPassword = Str::password(16, true, true, true, false);
    }

    public function saveUser()
    {
        $this->validate();

        $storedPath = null;

        try {
            // 1. Start Transaction
            DB::transaction(function () use ($storedPath) {

                // Create User
                $user = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone_number' => $this->phone_number,
                    'address' => $this->address,
                    'gender' => $this->gender,
                    'profile_picture' => $storedPath,
                    'department_id' => $this->department_id,
                    'hire_date' => $this->hire_date,
                    'role' => $this->role,
                    'is_active' => $this->is_active,
                    'password' => Hash::make(Str::random(32)),
                ]);

                // 2. Upload File (if exists)
                if ($this->profile_picture) {
                    $storedPath = $this->profile_picture->store('profile_pictures', 's3');
                }
                $token = Password::broker()->createToken($user);
                // 3. Send Email
                // [FIX] Queuing the modern email we created
                Mail::to($user->email)->queue(new UserInvitationMail($user, $token,tenant()->domain, tenant()->name));

                // 4. Log Activity
                $this->logActivity(
                    'user_created',
                    "admin created user {$user->name}",
                    ['user_id' => $user->id]
                );
            });

            // 5. Success Response
            LivewireAlert::title('Success')->success()->text('User created and credentials sent to email.')->show();
            return redirect()->route('admin.user-management');
        } catch (\Throwable $e) {
            // [FIX] Cleanup orphaned S3 file if DB transaction fails
            if ($storedPath) {
                Storage::disk('s3')->delete($storedPath);
            }

            Log::error('Error creating user.', [
                'error' => $e->getMessage(),
                'tenant_id' => tenant('id')
            ]);

            LivewireAlert::title('Error')->error()->text('Failed to create user. Please try again.')->show();
            return null;
        }
    }

    public function resetForm()
    {
        $this->reset(['name', 'phone_number', 'address', 'gender', 'profile_picture', 'department_id', 'hire_date', 'role', 'is_active', 'email', 'generatedPassword']);
        $this->dispatch('reset-file-input');
        $this->mount();
    }

    public function render()
    {
        return view('livewire.tenants.admin.create-new-user');
    }
}
