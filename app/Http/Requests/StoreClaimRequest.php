<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClaimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('view-finance');
    }

    public function rules(): array
    {
        return [
            'invoice_id' => ['required', 'exists:invoices,id'],
            'payer_id' => ['required', 'exists:payers,id'],
            'patient_insurance_id' => ['nullable', 'exists:patient_insurances,id'],
            'claim_amount' => ['required', 'numeric', 'min:0'],
        ];
    }
}
