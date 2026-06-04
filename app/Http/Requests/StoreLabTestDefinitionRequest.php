<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLabTestDefinitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-lab-tests');
    }

    public function rules(): array
    {
        return [
            'test_name' => 'required|string|max:255',
            'code' => 'required|string|unique:lab_test_definitions,code',
            'price' => 'required|numeric|min:0',
            'units' => 'required|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }
}
