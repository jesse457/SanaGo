<?php

namespace App\Livewire\Tenants\Receptionist;

use App\Services\PatientService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.receptionist')]
class RegisterPatient extends Component
{
    // Properties
    public string $first_name = '';

    public string $last_name = '';

    public ?int $age = null;

    public string $gender = '';

    public string $address = '';

    public string $phone = '';

    public string $email = '';

    // Validation
    protected function rules(): array
    {
        // Assuming you are using a tenancy package helper
        $tenantId = tenant('id');

        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'age' => 'required|integer|min:0|max:120',
            'gender' => 'required|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'phone' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('patients', 'phone')->where('tenant_id', $tenantId),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('patients', 'email')->where('tenant_id', $tenantId),
            ],
        ];
    }

    public function savePatient(PatientService $service): void
    {
        $data = $this->validate();

        try {
            // Delegate creation to Service.
            // The Service handles ID generation, Transaction, and Logging.
            $patient = $service->createPatient($data);

            LivewireAlert::title('Success')
                ->success()
                ->text("Patient {$this->first_name} {$this->last_name} registered successfully (ID: {$patient->patient_uid})")
                ->show();

            $this->resetForm();

        } catch (\Exception $e) {
            Log::error('Patient registration failed: '.$e->getMessage());

            // User-friendly error handling
            if (Str::contains($e->getMessage(), 'unique constraint')) {
                LivewireAlert::title('Duplicate Entry')
                    ->error()
                    ->text('A patient with this email or phone number already exists.')
                    ->show();
            } else {
                LivewireAlert::title('System Error')
                    ->error()
                    ->text('Could not register patient. Please contact support.')
                    ->show();
            }
        }
    }

    protected function resetForm(): void
    {
        $this->reset(['first_name', 'last_name', 'age', 'gender', 'address', 'phone', 'email']);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.tenants.receptionist.register-patient');
    }
}
