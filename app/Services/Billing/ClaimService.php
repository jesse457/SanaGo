<?php

namespace App\Services\Billing;

use App\Enums\ClaimStatus;
use App\Models\Claim;
use App\Models\Invoice;
use App\Models\Payer;
use Illuminate\Support\Facades\DB;

class ClaimService
{
    /**
     * Submit a new insurance claim for an invoice.
     */
    public function submitClaim(Invoice $invoice, Payer $payer, int $patientInsuranceId): Claim
    {
        return DB::transaction(function () use ($invoice, $payer, $patientInsuranceId) {
            $coveragePercent = (float) $payer->coverage_percentage;
            $claimAmount = round($invoice->total_amount * ($coveragePercent / 100), 2);

            return Claim::create([
                'invoice_id' => $invoice->id,
                'payer_id' => $payer->id,
                'patient_insurance_id' => $patientInsuranceId,
                'claim_amount' => $claimAmount,
                'submitted_at' => now(),
                'status' => ClaimStatus::Submitted,
            ]);
        });
    }

    /**
     * Approve a claim.
     */
    public function approve(Claim $claim): Claim
    {
        $claim->update(['status' => ClaimStatus::Approved]);

        return $claim;
    }

    /**
     * Reject a claim with a reason.
     */
    public function reject(Claim $claim, string $reason): Claim
    {
        $claim->update([
            'status' => ClaimStatus::Rejected,
            'rejection_reason' => $reason,
        ]);

        return $claim;
    }

    /**
     * Mark a claim as paid (insurance has remitted funds).
     */
    public function markPaid(Claim $claim, string $remittanceReference): Claim
    {
        $claim->update([
            'status' => ClaimStatus::Paid,
            'paid_at' => now(),
            'remittance_reference' => $remittanceReference,
        ]);

        return $claim;
    }
}
