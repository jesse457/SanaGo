<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CloseCashShiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-caisse');
    }

    public function rules(): array
    {
        return [
            // Optional: the cash counted at closing. Falling back to the
            // expected total is correct only when the value is absent.
            'closing_balance' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
