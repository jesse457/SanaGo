<?php

namespace App\Http\Requests;

use App\Enums\UrgencyLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * A lab order raised on its own: patient, test, urgency, reason. No
 * consultation is required — the request may carry one later as its origin,
 * but the order stands alone the moment the clinic needs it.
 */
class StoreLabRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('request-lab-tests');
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'test_definition_id' => ['required', 'integer', 'exists:lab_test_definitions,id'],
            'doctor_id' => ['nullable', 'integer', 'exists:users,id'],
            'urgency' => ['required', Rule::enum(UrgencyLevel::class)],
            'reason_for_test' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'patient_id.required' => 'Please choose the patient.',
            'test_definition_id.required' => 'Please select a lab test.',
            'test_definition_id.exists' => 'The selected lab test does not exist.',
            'urgency.*' => 'Urgency must be normal, urgent, or emergency.',
        ];
    }
}
