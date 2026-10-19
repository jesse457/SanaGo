<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRadiologyRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('view-radiology');
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'exists:patients,id'],
            'consultation_id' => ['nullable', 'exists:medical_records,id'],
            'imaging_type' => ['required', 'string', 'max:255'],
            'body_part' => ['required', 'string', 'max:255'],
            'clinical_notes' => ['nullable', 'string'],
        ];
    }
}
