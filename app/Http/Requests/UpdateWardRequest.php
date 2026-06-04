<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-infrastructure');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', Rule::unique('wards', 'code')->ignore($this->route('infrastructure'))],
            'floor' => 'nullable|string',
            'capacity' => 'required|integer',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ];
    }
}
