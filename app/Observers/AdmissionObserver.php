<?php

namespace App\Observers;

use App\Enums\AdmissionStatus;
use App\Models\Admission;
use App\Models\Invoice;
use App\Services\Billing\BillingService;

class AdmissionObserver
{
    /**
     * Fallback billing for an admission discharged outside AdmissionService.
     *
     * AdmissionService::discharge() already generates the full discharge
     * invoice (services, bed nights, observation fee, insurance split). When
     * that invoice exists the observer must stay out of the way, otherwise the
     * observation fee is billed twice on the same status change.
     */
    public function updated(Admission $admission): void
    {
        if (
            $admission->status === AdmissionStatus::Discharged
            && $admission->getOriginal('status') !== AdmissionStatus::Discharged->value
        ) {
            if ($this->hasInvoiceFor($admission)) {
                return;
            }

            $billing = app(BillingService::class);
            $invoice = $billing->findOrCreateDraftInvoice($admission->patient_id);
            $billing->addBillableItem(
                $invoice,
                'admission',
                $admission->id,
                'Admission (Discharged)',
                (float) $admission->observation_fee,
            );
        }
    }

    /**
     * Whether an invoice was already generated for this admission.
     */
    private function hasInvoiceFor(Admission $admission): bool
    {
        return Invoice::where('patient_id', $admission->patient_id)
            ->where('notes', BillingService::admissionInvoiceNote($admission))
            ->exists();
    }
}
