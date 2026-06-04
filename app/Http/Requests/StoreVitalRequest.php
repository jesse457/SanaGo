<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVitalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('record-vitals');
    }

    public function rules(): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'recorded_at' => 'required|date',
            'temperature' => 'required|numeric|between:30,45',
            'systolic_bp' => 'nullable|integer|min:0',
            'diastolic_bp' => 'nullable|integer|min:0',
            'heart_rate' => 'nullable|integer|min:0',
            'oxygen_saturation' => 'nullable|integer|between:0,100',
            'respiratory_rate' => 'nullable|integer|min:0',
            'is_abnormal' => 'boolean',
            'notes' => 'nullable|string',
        ];
    }
}
