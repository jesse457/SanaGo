<?php

namespace App\Http\Requests\Tenants\Doctor;

use Illuminate\Foundation\Http\FormRequest;

class StoreConsultationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Middleware handles this
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Handle JSON strings from FormData (Electron/React/Vue often sends it this way)
        if (is_string($this->prescription_items)) {
            $this->merge([
                'prescription_items' => json_decode($this->prescription_items, true),
            ]);
        }

        if (is_string($this->lab_items)) {
            $this->merge([
                'lab_items' => json_decode($this->lab_items, true),
            ]);
        }

        // Convert finalize to boolean if it's a string from FormData
        if ($this->has('finalize')) {
            $this->merge([
                'finalize' => filter_var($this->finalize, FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'complaint' => 'nullable|string',
            'diagnosis_text' => 'nullable|string',
            'general_notes' => 'nullable|string',
            'prescription_items' => 'nullable|array',
            'lab_items' => 'nullable|array',
            'finalize' => 'required|boolean',
            'id' => 'nullable|exists:medical_records,id',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->finalize && empty($this->complaint)) {
                $validator->errors()->add('complaint', 'You cannot sign a consultation without a Chief Complaint.');
            }
        });
    }
}
