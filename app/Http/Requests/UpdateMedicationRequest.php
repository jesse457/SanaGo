<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMedicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-drugs');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'dosage_units' => 'required|string',
            'min_stock_level' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ];
    }
}
