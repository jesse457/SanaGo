<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-supplies');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'unit_of_measure' => 'required|string|max:50',
            'cost_per_unit' => 'required|numeric|min:0',
            'current_stock' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ];
    }
}
