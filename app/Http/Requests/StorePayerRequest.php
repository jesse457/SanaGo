<?php

namespace App\Http\Requests;

use App\Enums\PayerType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('view-finance');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::enum(PayerType::class)],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'coverage_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'reimbursement_days' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }
}
