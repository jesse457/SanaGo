<?php

namespace App\Livewire\Tenants\Admin;

use App\Models\Department;
use App\Traits\UserActivitiesTrait;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.admin')]
class CreateNewUser extends Component
{
    use UserActivitiesTrait, WithFileUploads;

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
        $this->departments = Department::where('tenant_id', tenant('id'))->get(['id', 'name']);
        $this->generatePassword();
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'profile_picture') {
            try {
                $this->validateOnly($propertyName);
            } catch (ValidationException $e) {
                $this->profile_picture = null;
                throw $e;
            }
        }
    }

    protected function generatePassword()
    {
        $this->generatedPassword = Str::password(16, true, true, true, false);
    }

    public function saveUser(\App\Services\UserService $userService)
    {
        $this->validate();

        try {
            $userService->createUser([
                'name' => $this->name,
                'email' => $this->email,
                'phone_number' => $this->phone_number,
                'address' => $this->address,
                'gender' => $this->gender,
                'department_id' => $this->department_id,
                'hire_date' => $this->hire_date,
                'role' => $this->role,
                'is_active' => $this->is_active,
            ], $this->profile_picture, tenant('id'));

            LivewireAlert::title('Success')->success()->text('User created and credentials sent to email.')->show();

            return redirect()->route('admin.user-management');

        } catch (\Throwable $e) {
            // Extract real error
            $realError = $e;
            if (str_contains($e->getMessage(), 'current transaction is aborted') && $e->getPrevious()) {
                $realError = $e->getPrevious();
            }

            $uiMessage = $realError instanceof \Illuminate\Database\QueryException
                ? ($realError->errorInfo[2] ?? $realError->getMessage())
                : $realError->getMessage();

            // Error is already logged in Service, just show alert
            LivewireAlert::title('Error')->error()->text('Failed to create user: '.$uiMessage)->show();

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
