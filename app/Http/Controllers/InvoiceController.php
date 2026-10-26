<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Http\Requests\StoreInvoiceRequest;
use App\Models\CashShift;
use App\Models\Invoice;
use App\Models\Payer;
use App\Services\Billing\BillingService;
use App\Services\Billing\ClaimService;
use App\Services\Billing\SettlementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Invoice actions that outlive the old invoices screen.
 *
 * Bills are read and settled at the cash desk (CaisseController). This
 * controller keeps the status-focused view over the invoices ledger next to
 * the desk's targeted actions — record a payment, change a status, submit a
 * tiers payant claim, void an invoice.
 */
class InvoiceController extends Controller
{
    /**
     * The invoices ledger: every bill with its status, what was paid and what
     * remains. Billing stays at the desk; this screen reads.
     */
    public function index(Request $request, SettlementService $settlement): Response
    {
        $filters = [
            'search' => trim((string) $request->string('search')),
            'status' => (string) $request->string('status', 'all'),
        ];

        return Inertia::render('Finance/Invoices/Index', [
            'invoices' => $settlement->billList($filters),
            'filters' => $filters,
        ]);
    }

    /**
     * A bill is opened from the desk: pick the patient, write the charges.
     */
    public function create(): RedirectResponse
    {
        return redirect()->route('caisse.index');
    }

    public function store(StoreInvoiceRequest $request, BillingService $service)
    {
        $validated = $request->validated();
        $items = $validated['items'];
        unset($validated['items']);

        $service->createInvoiceWithItems($validated, $items);

        return redirect()->route('finance.invoices.index')
            ->with('success', 'Invoice created successfully.');
    }

    /**
     * The printable invoice document: A4 sheet or till receipt, with the
     * close-the-debt actions beside it. Printing must carry only this
     * document, never the surrounding app chrome.
     */
    public function show(Invoice $invoice, SettlementService $settlement): Response
    {
        return Inertia::render('Finance/Invoices/Show', [
            'invoice' => $settlement->invoiceDetail($invoice),
        ]);
    }

    public function updateStatus(Request $request, Invoice $invoice, BillingService $service)
    {
        $request->validate([
            'status' => 'required|in:paid,cancelled',
        ]);

        $status = InvoiceStatus::from($request->status);
        $service->updateStatus($invoice, $status);

        return back()->with('success', 'Invoice status updated.');
    }

    public function recordPayment(Request $request, Invoice $invoice, BillingService $service)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:'.$invoice->remaining_balance],
            'payment_method' => ['required', 'in:cash,mobile_money,bank_transfer'],
        ]);

        // The payment belongs to the cashier who took it: an any-user lookup
        // would put money on another cashier's till and break the shift report.
        $shift = CashShift::where('status', 'open')
            ->where('user_id', auth()->id())
            ->latest()
            ->first();

        if (! $shift) {
            return back()->with('error', 'No open cash shift. Please open a shift first.');
        }

        $service->recordPayment(
            $invoice,
            (float) $validated['amount'],
            $shift->id,
            $validated['payment_method']
        );

        return back()->with('success', 'Payment recorded successfully.');
    }

    /**
     * Submit an insurance claim (tiers payant) for an invoice.
     */
    public function submitClaim(Request $request, Invoice $invoice, ClaimService $claims)
    {
        $validated = $request->validate([
            'payer_id' => ['required', 'integer', 'exists:payers,id'],
            'patient_insurance_id' => ['required', 'integer', 'exists:patient_insurances,id'],
        ]);

        if ($invoice->claims()->exists()) {
            return back()->with('error', 'A claim has already been submitted for this invoice.');
        }

        $payer = Payer::query()->findOrFail($validated['payer_id']);

        $claims->submitClaim($invoice, $payer, (int) $validated['patient_insurance_id']);

        return back()->with('success', 'Claim submitted to '.$payer->name.'.');
    }

    public function destroy(Invoice $invoice)
    {
        /*
         * `claims` cascade on invoice_id, so deleting an invoice used to wipe
         * submitted and paid insurance claims with no warning. Claims are
         * money-relevant records: refuse the delete instead.
         */
        if ($invoice->claims()->exists()) {
            return back()->with(
                'error',
                'This invoice has insurance claims attached and cannot be deleted. Void the claims first.',
            );
        }

        if ($invoice->cashPayments()->exists()) {
            return back()->with(
                'error',
                'This invoice has recorded payments and cannot be deleted.',
            );
        }

        $invoice->items()->delete();
        $invoice->delete();

        return back()->with('success', 'Invoice deleted.');
    }
}
