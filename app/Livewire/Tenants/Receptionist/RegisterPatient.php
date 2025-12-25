<?php

namespace App\Livewire\Tenants\Receptionist;

use App\Models\Patient;
use App\Traits\UserActivitiesTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // [FIX] Added DB Facade
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.receptionist')]
class RegisterPatient extends Component
{
    use UserActivitiesTrait;

    public string $first_name = '';
    public string $last_name = '';
    public ?int $age = null;
    public string $gender = '';
    public string $address = '';
    public string $phone = '';
    public string $email = '';
    public array $doctors = [];
    public array $foundPatients = [];

    protected function rules(): array
    {
        $tenantId = tenant('id');
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'age' => 'required|integer|min:0',
            'gender' => 'required|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'phone' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('patients', 'phone')->where('tenant_id', $tenantId)->ignore(null, 'phone'),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('patients', 'email')->where('tenant_id', $tenantId),
            ],
        ];
    }

    public function savePatient(): void
    {
        $this->validate();

        try {
            // [FIX] Start Transaction on the direct connection
            DB::connection('pgsql_transaction')->transaction(function ($static) {
                $patientUid = 'PT-' . Str::upper(Str::random(6));
                while (Patient::where('patient_uid', $patientUid)->exists()) {
                    $patientUid = 'PT-' . Str::upper(Str::random(6));
                }

                $patient = Patient::create([
                    'patient_uid' => $patientUid,
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'age' => $this->age,
                    'gender' => $this->gender,
                    'address' => $this->address,
                    'phone' => $this->phone,
                    'email' => $this->email,
                ]);

                $name = Auth::user()->name;

                $this->logActivity(
                    'patient_registered',
                    "Registered patient #$patient->id ($patientUid): {$this->first_name} {$this->last_name} by Receptionist {$name}",
                    [
                        'patient_id' => $patient->id,
                        'receptionist_id' => Auth::id(),
                    ]
                );
            });


            LivewireAlert::title('Success')
                ->success()
                ->text('Patient ' . $this->first_name . ' ' . $this->last_name . ' has been successfully Registered')
                ->show();

            $this->resetForm();
        } catch (\Exception $e) {


            Log::error('Patient registration failed: ' . $e->getMessage());

            // Handle Unique Violations specifically
            if (Str::contains($e->getMessage(), 'unique constraint')) {
                LivewireAlert::title('Error')->error()->text('A patient with this email or phone already exists.')->show();
            } else {
                LivewireAlert::title('Error')->error()->text('Server Error please Contact us in Feedback if this error persist')->show();
            }
        }
    }

    protected function resetForm(): void
    {
        $this->first_name = '';
        $this->last_name = '';
        $this->age = null;
        $this->gender = '';
        $this->address = '';
        $this->phone = '';
        $this->email = '';
        $this->resetValidation();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.tenants.receptionist.register-patient');
    }
}
