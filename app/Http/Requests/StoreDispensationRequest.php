<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDispensationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('dispense-drugs');
    }

    public function rules(): array
    {
        return [
            'patient_id' => 'nullable|exists:patients,id',
            'prescription_id' => 'nullable|exists:prescriptions,id',
            'is_walk_in' => 'required|boolean',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.medication_id' => 'required|exists:medications,id',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }
}
