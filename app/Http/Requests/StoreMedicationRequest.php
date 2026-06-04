<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicationRequest extends FormRequest
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
            'batches' => 'nullable|array',
            'batches.*.batch_number' => 'required|string',
            'batches.*.expiry_date' => 'required|date',
            'batches.*.quantity' => 'required|integer|min:1',
            'batches.*.purchase_price' => 'required|numeric|min:0',
        ];
    }
}
