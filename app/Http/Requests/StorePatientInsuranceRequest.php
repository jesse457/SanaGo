<?php

namespace App\Http\Requests;

use App\Enums\BeneficiaryType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePatientInsuranceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('view-finance');
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'exists:patients,id'],
            'payer_id' => ['required', 'exists:payers,id'],
            'policy_number' => ['required', 'string', 'max:255'],
            'card_number' => ['nullable', 'string', 'max:255'],
            'valid_from' => ['required', 'date'],
            'valid_until' => ['required', 'date', 'after:valid_from'],
            'beneficiary_type' => ['required', Rule::enum(BeneficiaryType::class)],
            'is_primary' => ['boolean'],
        ];
    }
}
