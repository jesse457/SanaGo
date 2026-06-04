<?php

namespace App\Http\Requests;

use App\Enums\UrgencyLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmergencyCaseRequest extends FormRequest
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
            'arrived_at' => ['required', 'date'],
            'emergency_fee' => ['nullable', 'numeric', 'min:0'],
            'treatment_notes' => ['nullable', 'string'],
        ];
    }
}
