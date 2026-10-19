<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClaimRequest;
use App\Models\Claim;
use App\Models\Invoice;
use App\Models\Payer;
use App\Services\Billing\ClaimService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClaimController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Finance/Claims/Index', [
            'claims' => Claim::with([
                'invoice:id,invoice_number,total_amount,patient_id',
                'invoice.patient:id,first_name,last_name,patient_uid',
                'payer:id,name,type',
            ])
                ->orderBy('created_at', 'desc')
                ->paginate(20),
        ]);
    }

    public function show(Claim $claim): Response
    {
        return Inertia::render('Finance/Claims/Show', [
            'claim' => $claim->load([
                'invoice.patient:id,first_name,last_name,patient_uid',
                'invoice.items',
                'payer:id,name,type,coverage_percentage',
                'patientInsurance:id,policy_number,card_number',
            ]),
        ]);
    }

    /**
     * Submit a new claim for an invoice.
     */
    public function store(StoreClaimRequest $request, ClaimService $claimService)
    {
        $validated = $request->validated();

        $invoice = Invoice::findOrFail($validated['invoice_id']);
        $payer = Payer::findOrFail($validated['payer_id']);

        $claimService->submitClaim($invoice, $payer, $validated['patient_insurance_id']);

        return back()->with('success', 'Claim submitted successfully.');
    }

    /**
     * Update claim status (approve, reject, mark paid).
     */
    public function update(Request $request, Claim $claim, ClaimService $claimService)
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,reject,mark_paid',
            'rejection_reason' => 'required_if:action,reject|string|nullable|max:1000',
            'remittance_reference' => 'required_if:action,mark_paid|string|nullable|max:100',
        ]);

        match ($validated['action']) {
            'approve' => $claimService->approve($claim),
            'reject' => $claimService->reject($claim, $validated['rejection_reason'] ?? ''),
            'mark_paid' => $claimService->markPaid($claim, $validated['remittance_reference'] ?? ''),
        };

        return back()->with('success', 'Claim updated successfully.');
    }
}
