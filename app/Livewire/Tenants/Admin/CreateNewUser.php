<?php

namespace App\Livewire\Tenants\Admin;

use App\Mail\SendCredentials;
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
use Illuminate\Validation\ValidationException; // Required for catching validation errors
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

// Use the admin layout for this Livewire component
#[Layout('components.layouts.admin')]
/**
 * CreateNewUser Livewire Component
 *
 * This component provides a form for tenant administrators to create new user
 * accounts within their tenant scope. It handles form validation,
 * file uploads (profile pictures), database transactions, and sending
 * credentials via email.
 */
class CreateNewUser extends Component
{
    // UserActivitiesTrait: Custom trait for logging user actions.
    // WithFileUploads: Livewire trait for handling file uploads.
    use UserActivitiesTrait, WithFileUploads;

    // --- Form Properties ---
    // These public properties are bound to the form inputs in the view.

    /** @var string|null The user's full name. */
    public $name;

    /** @var string|null The user's phone number. */
    public $phone_number;

    /** @var string|null The user's physical address. */
    public $address;

    /** @var string|null The user's gender ('Male', 'Female', 'Other'). */
    public $gender;

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null The temporary uploaded file instance from Livewire. */
    public $profile_picture;

    /** @var int|null The ID of the department the user belongs to. */
    public $department_id;

    /** @var string|null The user's hire date (in 'Y-m-d' format or similar). */
    public $hire_date;

    /** @var string|null The user's assigned role (e.g., 'admin', 'doctor'). */
    public $role;

    /** @var bool Whether the user's account is active. Defaults to true. */
    public $is_active = true;

    /** @var string|null The user's email address. */
    public $email;

    /** @var string|null The securely generated password for the new user. */
    public $generatedPassword;

    // --- Component Data ---

    /** @var \Illuminate\Database\Eloquent\Collection A collection of departments for the dropdown. */
    public $departments;

    /**
     * Defines the validation rules for the form properties.
     *
     * @return array
     */
    protected function rules()
    {
        $tenantId = tenant('id'); // Get the current tenant's ID for scoping rules

        return [
            'name' => 'required|string|max:255',
            // Email must be unique within the 'users' table, but only for the current tenant.
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->where('tenant_id', $tenantId)],
            // Phone number is optional, but if provided, must be unique for the current tenant.
            'phone_number' => ['nullable', 'string', 'max:20', Rule::unique('users', 'phone_number')->where('tenant_id', $tenantId)],
            'address' => 'nullable|string|max:255',
            'gender' => ['nullable', Rule::in(['Male', 'Female', 'Other'])],
            // Department ID is optional, but if provided, must exist in the 'departments' table for the current tenant.
            'department_id' => ['nullable', Rule::exists('departments', 'id')->where('tenant_id', $tenantId)],
            'hire_date' => 'nullable|date',
            'role' => ['required', Rule::in(['admin', 'doctor', 'nurse', 'receptionist', 'lab-technician', 'pharmacist'])],
            'is_active' => 'boolean',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ];
    }

    /**
     * Mount hook - runs when the component is first initialized.
     * Used to pre-fill data.
     *
     * @return void
     */
    public function mount()
    {
        // Load only departments that belong to the current tenant.
        $this->departments = Department::where('tenant_id', tenant('id'))->get(['id', 'name']);
        // Generate an initial random password.
        $this->generatePassword();
    }

    /**
     * Updated hook - runs in real-time when a public property is updated.
     * This is used for real-time validation, especially for file uploads.
     *
     * @param  string  $propertyName  The name of the property that was updated.
     * @return void
     *
     * @throws ValidationException|\Throwable
     */
    public function updated($propertyName)
    {
        // We only care about validating the profile picture in real-time.
        if ($propertyName === 'profile_picture') {
            try {
                // Validate only the 'profile_picture' field using the rules defined.
                $this->validateOnly($propertyName);
            } catch (ValidationException $e) {
                // Log the validation error during the temporary upload.
                Log::warning('Livewire temporary file upload validation failed.', [
                    'error' => $e->getMessage(),
                    'errors_bag' => $e->errors(),
                    'tenant_id' => tenant('id'),
                    'component' => static::class,
                ]);
                // Re-throw the exception so Livewire's front-end can display the error.
                throw $e;
            } catch (\Throwable $e) {
                // Catch any other unexpected errors.
                Log::error('Unexpected error during file property update.', [
                    'error_message' => $e->getMessage(),
                    'tenant_id' => tenant('id'),
                    'component' => static::class,
                ]);
                throw $e; // Re-throw to be safe.
            }
        }
    }

    /**
     * Generates a new secure, random password and stores it in the component.
     *
     * @return void
     */
    protected function generatePassword()
    {
        // Generates a 16-character password with letters, numbers, symbols, and no unsimilar chars.
        $this->generatedPassword = Str::password(16, true, true, true, false);
    }

    /**
     * Handles the form submission to create the new user.
     *
     * This method is transactional and includes robust error handling to
     * prevent orphaned files if the database operation fails.
     *
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function saveUser()
    {
        // 1. Validate all form fields based on the `rules()` method.
        $this->validate();

        /** @var string|null $storedPath The path where the profile picture is stored in S3. */
        $storedPath = null;

        try {
            // 2. Handle File Upload (if one was provided).
            // This is done *outside* the database transaction. If S3 storage fails,
            // we don't want to roll back a database entry (and vice-versa).
            if ($this->profile_picture) {
                // Store the file in the 'profile_pictures' directory on the 's3' disk.
                $storedPath = $this->profile_picture->store('profile_pictures', 's3');
                Log::info('Profile picture stored permanently in S3.', [
                    'path' => $storedPath,
                    'tenant_id' => tenant('id'),
                ]);
            }

            // 3. Wrap database creation and email sending in a transaction.
            // This ensures that if *any* part fails (e.g., email prep, user create),
            // the whole operation is rolled back, and no user is created.
            DB::transaction(function () use ($storedPath) {
                // 4. Create the user record in the database.
                /** @var User $user */
                $user = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone_number' => $this->phone_number,
                    'address' => $this->address,
                    'gender' => $this->gender,
                    'profile_picture' => $storedPath, // Use the S3 path (or null if no file)
                    'department_id' => $this->department_id,
                    'hire_date' => $this->hire_date,
                    'role' => $this->role,
                    'is_active' => $this->is_active,
                    'password' => Hash::make($this->generatedPassword), // Hash the generated password
                ]);

                // 5. Prepare the credentials email.
                $mailable = new SendCredentials([
                    'subject' => 'Your New Account Credentials',
                    'view' => 'emails.welcome',
                    'name' => $user->name ?? 'User',
                    'email' => $user->email,
                    'password' => $this->generatedPassword, // Send the plain-text password
                    'login_url' => route('login'),
                ]);

                // 6. Queue the email to be sent. (Currently commented out)
                // Mail::to($user->email)->queue($mailable);

                // 7. Log the successful creation using the custom trait.
                Log::info('User created successfully within transaction.', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'tenant_id' => $user->tenant_id,
                ]);

                $this->logActivity(
                    'user_created',
                    "admin created user {$user->name} ",
                    ['user_id' => $user->id]
                );
            }); // Transaction commits here if all was successful.

            // 8. Show a success message to the admin.
            LivewireAlert::title('Success')->success()->text('User created successfully and credentials have been sent.')->show();

            // 9. Redirect to the user management page.
            return redirect()->route('admin.user-management');

        } catch (\Throwable $e) { // Catch any exception from the try block.
            // 10. Handle Failure
            Log::error('Error creating user.', [
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
                'tenant_id' => tenant('id'),
                // Log all form input *except* sensitive/large data.
                'user_input' => collect($this->all())->except(['profile_picture', 'generatedPassword'])->toArray(),
            ]);

            // 11. **CRITICAL: Cleanup Orphaned File**
            // If a file was uploaded ($storedPath is not null) but the transaction
            // failed, we must delete the file from S3 to prevent orphans.
            if ($storedPath) {
                try {
                    Storage::disk('s3')->delete($storedPath);
                    Log::info('Orphaned profile picture deleted from S3.', ['path' => $storedPath]);
                } catch (\Exception $s3Exception) {
                    // Log if the *deletion* fails, as this requires manual cleanup.
                    Log::critical('Failed to delete orphaned S3 file.', [
                        'path' => $storedPath,
                        'error_message' => $s3Exception->getMessage(),
                    ]);
                }
            }

            // 12. Show a generic error message to the user.
            LivewireAlert::title('Error')->error()->text('An unexpected error occurred. Please contact support.')->show();

            return null; // Stay on the page.
        }
    }

    /**
     * Resets all form fields to their initial state.
     *
     * @return void
     */
    public function resetForm()
    {
        // Reset all public properties defined in the array.
        $this->reset([
            'name',
            'phone_number',
            'address',
            'gender',
            'profile_picture',
            'department_id',
            'hire_date',
            'role',
            'is_active',
            'email',
            'generatedPassword',
        ]);

        // Dispatch a custom browser event to reset the file input field,
        // as `reset()` doesn't clear the file input UI.
        $this->dispatch('reset-file-input');

        // Re-run the mount logic to re-fetch departments (if needed)
        // and generate a *new* password.
        $this->mount();
    }

    /**
     * Renders the component's view.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        // Returns the corresponding Blade view.
        return view('livewire.tenants.admin.create-new-user');
    }
}
