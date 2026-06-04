<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConsultationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create-consultations');
    }

    public function rules(): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'chief_complaint' => 'required|string',
            'history_of_present_illness' => 'nullable|string',
            'physical_exam' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'treatment_plan' => 'nullable|string',
            'prescription_items' => 'nullable|array',
            'prescription_items.*.medication_id' => 'nullable|exists:medications,id',
            'prescription_items.*.drug_name' => 'required|string|max:255',
            'prescription_items.*.dosage' => 'required|string',
            'prescription_items.*.frequency' => 'required|string',
            'prescription_items.*.duration' => 'required|string',
            'prescription_items.*.quantity' => 'required|integer|min:1',
            'prescription_items.*.instructions' => 'nullable|string',
            'lab_requests' => 'nullable|array',
            'lab_requests.*.test_definition_id' => 'required|exists:lab_test_definitions,id',
            'lab_requests.*.urgency' => 'required|in:routine,urgent,stat',
            'lab_requests.*.reason_for_test' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240',
        ];
    }
}
