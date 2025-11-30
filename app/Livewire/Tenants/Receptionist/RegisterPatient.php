<?php

namespace App\Livewire\Tenants\Receptionist;

use App\Models\Patient;
use App\Traits\UserActivitiesTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * RegisterPatient component for handling patient registration in the receptionist dashboard.
 * This component provides a form for registering new patients and handles the validation,
 * creation, and activity logging for patient registration.
 */
#[Layout('components.layouts.receptionist')]
class RegisterPatient extends Component
{
    use UserActivitiesTrait;

    // Form properties for patient registration
    /** @var string Patient's first name */
    public string $first_name = '';

    /** @var string Patient's last name */
    public string $last_name = '';

    /** @var int|null Patient's age */
    public ?int $age = null;

    /** @var string Patient's gender (male, female, other) */
    public string $gender = '';

    /** @var string Patient's address */
    public string $address = '';

    /** @var string Patient's phone number */
    public string $phone = '';

    /** @var string Patient's email address */
    public string $email = '';

    // Data for dropdowns/search results (not used in this component)
    /** @var array List of doctors (not used in this component) */
    public array $doctors = [];

    /** @var array Found patients (not used in this component) */
    public array $foundPatients = [];

    /**
     * Defines the validation rules for the component.
     * Using a method allows for dynamic rules, such as including the tenant_id
     * in the unique validation rule.
     *
     * @return array The validation rules.
     */
    protected function rules(): array
    {
        $tenantId = tenant('id'); // Cache tenant ID

        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'age' => 'required|integer|min:0',
            'gender' => 'required|in:male,female,other', // Ensure these match DB check constraint!
            'address' => 'nullable|string|max:500',
            'phone' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('patients', 'phone')
                    ->where('tenant_id', $tenantId)
                    // Added a condition to ignore the unique check if phone is empty
                    ->ignore(null, 'phone'),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('patients', 'email')->where('tenant_id', $tenantId),
            ],
        ];
    }

    /**
     * Handles patient registration logic.
     * Validates the form data, creates a new patient record with a unique ID,
     * logs the activity, and shows a success or error message.
     */
    public function savePatient(): void
    {
        // Validate form input using the rules() method
        $this->validate();

        try {
            // Generate a unique patient UID
            $patientUid = 'PT-'.Str::upper(Str::random(6));
            while (Patient::where('patient_uid', $patientUid)->exists()) {
                $patientUid = 'PT-'.Str::upper(Str::random(6));
            }

            // Create the patient record in the database
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

            // Get the name of the currently authenticated user
            $name = Auth::user()->name;

            // Log the registration activity
            $this->logActivity(
                'patient_registered',
                "Registered patient #$patient->id ($patientUid): {$this->first_name} {$this->last_name} by Receptionist {$name}",
                [
                    'patient_id' => $patient->id,
                    'receptionist_id' => Auth::id(),
                ]
            );

            // Show a success alert to the user
            LivewireAlert::title('Success')
                ->success()
                ->text('Patient '.$this->first_name.' '.$this->last_name.' has been successfully Registered')
                ->show();

            // Reset the form fields
            $this->resetForm();
        } catch (\Exception $e) {
            // Log any errors that occur during registration
            Log::error('Patient registration failed: '.$e->getMessage());

            // Show an error alert to the user
            LivewireAlert::title('Error')
                ->error()
                ->text('Server Error please Contact us in Feedback if this error persist')
                ->show();
        }
    }

    /**
     * Resets the form fields and validation errors.
     * This method is called after a successful patient registration.
     */
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

    /**
     * Renders the Livewire component view.
     *
     * @return \Illuminate\View\View The view for the patient registration form
     */
    public function render(): \Illuminate\View\View
    {
        return view('livewire.tenants.receptionist.register-patient');
    }
}
