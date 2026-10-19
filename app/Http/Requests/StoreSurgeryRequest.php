<?php

namespace App\Http\Requests;

use App\Enums\UrgencyLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSurgeryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create-consultations');
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'exists:patients,id'],
            'surgeon_id' => ['nullable', 'exists:users,id'],
            'surgeon_name' => ['nullable', 'required_without:surgeon_id', 'string', 'max:255'],
            'anesthetist_id' => ['nullable', 'exists:users,id'],
            'anesthetist_name' => ['nullable', 'required_without:anesthetist_id', 'string', 'max:255'],
            'bed_id' => ['nullable', 'exists:beds,id'],
            'surgery_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'scheduled_at' => ['required', 'date'],
            'estimated_duration_minutes' => ['nullable', 'integer', 'min:1'],
            'urgency' => ['required', Rule::enum(UrgencyLevel::class)],
            'surgery_fee' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'surgeon_name.required_without' => 'Please select a surgeon or enter an external surgeon name.',
        ];
    }
}
