<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVitalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('record-vitals');
    }

    public function rules(): array
    {
        return [
            'recorded_at' => 'required|date',
            'temperature' => 'required|numeric|between:30,45',
            'systolic_bp' => 'nullable|integer',
            'diastolic_bp' => 'nullable|integer',
            'heart_rate' => 'nullable|integer',
            'oxygen_saturation' => 'nullable|integer|between:0,100',
            'respiratory_rate' => 'nullable|integer',
            'is_abnormal' => 'boolean',
            'notes' => 'nullable|string',
        ];
    }
}
