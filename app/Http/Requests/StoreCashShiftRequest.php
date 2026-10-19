<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCashShiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-caisse');
    }

    public function rules(): array
    {
        return [
            'payment_point' => ['required', 'string', 'max:255'],
            'opening_balance' => ['required', 'numeric', 'min:0'],
        ];
    }
}
