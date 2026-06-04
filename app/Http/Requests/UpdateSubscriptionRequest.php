<?php

namespace App\Http\Requests;

use App\Enums\SubscriptionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-tenants') ?? false;
    }

    public function rules(): array
    {
        return [
            'plan_name' => ['required', 'string', 'max:100'],
            'billing_cycle' => ['required', 'string', Rule::in(['monthly', 'yearly'])],
            'price' => ['required', 'numeric', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'features_json' => ['nullable', 'array'],
            'status' => ['required', Rule::enum(SubscriptionStatus::class)],
        ];
    }
}
