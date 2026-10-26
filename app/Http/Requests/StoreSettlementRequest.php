<?php

namespace App\Http\Requests;

use App\Enums\PaymentMethod;
use App\Services\Billing\CounterCatalogue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * The counter's "pay service" action: a patient, what is being paid for, and the
 * money handed over.
 *
 * Two ways to say what is paid for:
 *  - `picks` names the record behind each line (a drug, a lab test, a bed day of
 *    the open stay, a price-list service) plus how many units, so the amount is
 *    always read from the catalogue instead of being typed;
 *  - `item_ids` settles lines already written on the patient's bill.
 */
class StoreSettlementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-caisse');
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'exists:patients,id'],
            // Either already-billed charges or catalogue picks must be present;
            // the controller checks that the combination is non-empty.
            'item_ids' => ['nullable', 'array'],
            'item_ids.*' => ['integer', 'exists:invoice_items,id'],
            'picks' => ['nullable', 'array', 'max:50'],
            'picks.*.source' => ['required', Rule::in([
                CounterCatalogue::SOURCE_SERVICE,
                CounterCatalogue::SOURCE_MEDICATION,
                CounterCatalogue::SOURCE_LAB_TEST,
                CounterCatalogue::SOURCE_ADMISSION_BED,
                CounterCatalogue::SOURCE_ADMISSION_SERVICE,
            ])],
            'picks.*.id' => ['required', 'integer', 'min:1'],
            'picks.*.quantity' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
            'amount_tendered' => ['required', 'numeric', 'min:0'],
            // Idempotency key generated per counter attempt.
            'client_uuid' => ['nullable', 'string', 'max:64'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function settlementPayload(): array
    {
        return $this->validated();
    }
}
