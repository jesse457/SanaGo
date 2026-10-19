<?php

namespace App\Http\Requests;

use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCashPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-caisse');
    }

    public function rules(): array
    {
        return [
            'invoice_id' => ['nullable', 'exists:invoices,id'],
            'patient_id' => ['nullable', 'exists:patients,id'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
        ];
    }
}
