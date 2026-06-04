<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-infrastructure');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:wards,code',
            'floor' => 'nullable|string',
            'capacity' => 'required|integer',
            'description' => 'nullable|string',
        ];
    }
}
