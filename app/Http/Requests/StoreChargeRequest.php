<?php

namespace App\Http\Requests;

use App\Enums\PatientChargeCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * The cashier writing charges by hand: what the patient consumed during the
 * stay, priced on the spot. Lines arrive in one batch so a multi-line bill is
 * built in a single submit.
 */
class StoreChargeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-caisse');
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'exists:patients,id'],
            'lines' => ['required', 'array', 'min:1', 'max:50'],
            'lines.*.description' => ['required', 'string', 'max:255'],
            'lines.*.category' => ['required', Rule::enum(PatientChargeCategory::class)],
            'lines.*.quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'lines.*.unit_price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
