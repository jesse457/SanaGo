<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLabTestDefinitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-lab-tests');
    }

    public function rules(): array
    {
        return [
            'test_name' => 'required|string|max:255',
            'code' => ['required', 'string', Rule::unique('lab_test_definitions', 'code')->ignore($this->route('test'))],
            'price' => 'required|numeric|min:0',
            'units' => 'required|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }
}
