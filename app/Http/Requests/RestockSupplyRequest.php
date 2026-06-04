<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RestockSupplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-supplies');
    }

    public function rules(): array
    {
        return [
            'add_amount' => ['required', 'integer', 'min:1'],
        ];
    }
}
