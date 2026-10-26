<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Correcting a hand-written charge before the patient pays it.
 *
 * Description, quantity and price are each optional so the counter can fix one
 * mistake without resending the whole line.
 */
class UpdateChargeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-caisse');
    }

    public function rules(): array
    {
        return [
            'description' => ['sometimes', 'required', 'string', 'max:255'],
            'quantity' => ['sometimes', 'required', 'integer', 'min:1', 'max:100000'],
            'unit_price' => ['sometimes', 'required', 'numeric', 'min:0'],
        ];
    }
}
