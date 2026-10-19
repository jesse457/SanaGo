<?php

namespace App\Http\Requests;

use App\Enums\CertificateType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCertificateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create-consultations');
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'exists:patients,id'],
            'consultation_id' => ['nullable', 'exists:medical_records,id'],
            // 'type' => ['required', Rule::enum(CertificateType::class)],
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
            'diagnosis_summary' => ['nullable', 'string'],
            'recommendations' => ['nullable', 'string'],
            'employer_name' => ['nullable', 'string', 'max:255'],
            'employer_address' => ['nullable', 'string', 'max:255'],
        ];
    }
}
