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
        // We use the result of the transaction to determine the redirect
        DB::transaction(function () use (&$storedPath) {

            // 1. Upload File
            if ($this->profile_picture) {
                $storedPath = $this->profile_picture->store('profile_pictures', 's3');
            }

            // 2. Create User
            $user = User::create([
                'name'            => $this->name,
                'email'           => $this->email,
                'phone_number'    => $this->phone_number,
                'address'         => $this->address,
                'gender'          => $this->gender,
                'profile_picture' => $storedPath,
                'department_id'   => $this->department_id,
                'hire_date'       => $this->hire_date,
                'role'            => $this->role,
                'is_active'       => $this->is_active,
                'password'        => Hash::make(Str::random(32)),
                'tenant_id'       => tenant('id'),
            ]);

            // 3. Create Token and Queue Mail
            $token = Password::broker()->createToken($user);

            Mail::to($user->email)->queue(new UserInvitationMail(
                $user,
                $token,
                tenant()->domains->first()->domain ?? request()->getHost(),
                tenant('name')
            ));

            $this->logActivity('user_created', "admin created user {$user->name}", ['user_id' => $user->id]);
        });

        // Only runs if transaction succeeds
        LivewireAlert::title('Success')->success()->text('User created and credentials sent to email.')->show();
        return redirect()->route('admin.user-management');

    } catch (\Throwable $e) {
        // 1. Clean up S3 if database failed
        if ($storedPath) {
            Storage::disk('s3')->delete($storedPath);
        }

        // 2. DIG FOR THE REAL ERROR
        // If the error is "Transaction aborted", the real error is usually inside getPrevious()
        $realError = $e;
        if (str_contains($e->getMessage(), 'current transaction is aborted') && $e->getPrevious()) {
            $realError = $e->getPrevious();
        }

        // 3. PREPARE CLEAN MESSAGE FOR UI
        // If it's a database query error, the message includes the full SQL.
        // We want to show the 'SQLSTATE' message, not the whole query.
        $uiMessage = $realError->getMessage();

        if ($realError instanceof \Illuminate\Database\QueryException) {
            // This grabs just the error code and message (e.g., "Duplicate entry 'x' for key 'y'")
            // ignoring the 50 lines of SQL context.
            $uiMessage = $realError->errorInfo[2] ?? $realError->getMessage();
        }

        // 4. LOG THE FULL TECHNICAL DETAILS
        Log::error('USER_CREATION_FAILED', [
            'original_message' => $e->getMessage(),
            'root_cause'       => $realError->getMessage(),
            'file'             => $realError->getFile(),
            'line'             => $realError->getLine(),
        ]);

        // 5. SHOW HUMAN READABLE ERROR
        LivewireAlert::title('Error')->error()->text('Failed to create user: ' . $uiMessage)->show();

        // Do not redirect; let the user see the error and fix the form
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
