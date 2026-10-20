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
            // A shift belongs to a till; `payment_point` is derived from it so
            // existing receipts keep printing the same label.
            'cash_point_id' => ['required', 'exists:cash_points,id'],
            'opening_balance' => ['required', 'numeric', 'min:0'],
        ];
    }
}
