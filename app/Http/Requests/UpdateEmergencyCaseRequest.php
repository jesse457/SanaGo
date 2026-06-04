<?php

namespace App\Http\Requests;

use App\Enums\EmergencyStatus;
use App\Enums\UrgencyLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmergencyCaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create-admissions');
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'exists:patients,id'],
            'attending_doctor_id' => ['nullable', 'exists:users,id'],
            'triage_nurse_id' => ['nullable', 'exists:users,id'],
            'arrival_mode' => ['nullable', 'string', 'max:100'],
            'chief_complaint' => ['required', 'string', 'max:500'],
            'primary_assessment' => ['nullable', 'string'],
            'triage_level' => ['required', Rule::enum(UrgencyLevel::class)],
            'status' => ['required', Rule::enum(EmergencyStatus::class)],
            'disposition' => ['nullable', 'string', 'max:255'],
            'admission_id' => ['nullable', 'exists:admissions,id'],
            'arrived_at' => ['required', 'date'],
            'triaged_at' => ['nullable', 'date'],
            'stabilized_at' => ['nullable', 'date'],
            'discharged_at' => ['nullable', 'date'],
            'emergency_fee' => ['nullable', 'numeric', 'min:0'],
            'treatment_notes' => ['nullable', 'string'],
            'discharge_instructions' => ['nullable', 'string'],
        ];
    }
}
