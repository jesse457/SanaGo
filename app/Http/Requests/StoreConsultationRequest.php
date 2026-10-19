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
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'chief_complaint' => ['required', 'string', 'max:1000'],
            'history_of_present_illness' => ['nullable', 'string', 'max:5000'],
            'physical_exam' => ['nullable', 'string', 'max:5000'],
            'diagnosis' => ['nullable', 'string', 'max:2000'],
            'treatment_plan' => ['nullable', 'string', 'max:5000'],
            'notes' => ['nullable', 'string', 'max:5000'],

            // Vital signs
            'temperature' => ['nullable', 'numeric', 'min:30', 'max:45'],
            'systolic_bp' => ['nullable', 'integer', 'min:50', 'max:300'],
            'diastolic_bp' => ['nullable', 'integer', 'min:30', 'max:200'],
            'heart_rate' => ['nullable', 'integer', 'min:20', 'max:300'],
            'oxygen_saturation' => ['nullable', 'integer', 'min:0', 'max:100'],
            'respiratory_rate' => ['nullable', 'integer', 'min:0', 'max:100'],
            'weight_kg' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'height_cm' => ['nullable', 'numeric', 'min:0', 'max:300'],
            // Follow-up
            'follow_up_date' => ['nullable', 'date', 'after:today'],
            'follow_up_instructions' => ['nullable', 'string', 'max:2000'],

            'prescription_items' => ['nullable', 'array'],
            'prescription_items.*.medication_id' => ['nullable', 'integer', 'exists:medications,id'],
            'prescription_items.*.drug_name' => ['required', 'string', 'max:255'],
            'prescription_items.*.dosage' => ['nullable', 'string', 'max:100'],
            'prescription_items.*.frequency' => ['nullable', 'string', 'max:100'],
            'prescription_items.*.duration' => ['nullable', 'string', 'max:100'],
            'prescription_items.*.quantity' => ['required', 'integer', 'min:1'],
            'prescription_items.*.instructions' => ['nullable', 'string', 'max:500'],

            'lab_requests' => ['nullable', 'array'],
            'lab_requests.*.test_definition_id' => ['required', 'integer', 'exists:lab_test_definitions,id'],
            'lab_requests.*.urgency' => ['required', 'string', 'in:routine,urgent,stat'],
            'lab_requests.*.reason_for_test' => ['nullable', 'string', 'max:1000'],

            'attachments' => ['nullable', 'array', 'max:10'],
            'attachments.*' => ['file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,gif,doc,docx,txt'],
        ];
    }

    public function messages(): array
    {
        return [
            'patient_id.required' => 'Please select a patient before saving.',
            'patient_id.exists' => 'The selected patient does not exist.',
            'chief_complaint.required' => 'The chief complaint is required.',
            'prescription_items.*.drug_name.required' => 'Drug name is required for each prescription item.',
            'prescription_items.*.quantity.min' => 'Quantity must be at least 1.',
            'lab_requests.*.test_definition_id.required' => 'Please select a lab test.',
            'lab_requests.*.test_definition_id.exists' => 'The selected lab test does not exist.',
            'lab_requests.*.urgency.in' => 'Urgency must be routine, urgent, or stat.',
            'attachments.max' => 'You can upload a maximum of 10 files.',
            'attachments.*.max' => 'Each file must be under 10MB.',
            'attachments.*.mimes' => 'Allowed file types: PDF, JPG, PNG, GIF, DOC, DOCX, TXT.',
        ];
    }
}
