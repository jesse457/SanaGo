<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Http\Requests\StoreInvoiceRequest;
use App\Models\CashShift;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Payer;
use App\Services\Billing\BillingService;
use App\Services\Billing\ClaimService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['patient:id,first_name,last_name,patient_uid'])
            ->withCount('items');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('invoice_number', 'like', "%{$request->search}%")
                    ->orWhereHas('patient', function ($sq) use ($request) {
                        $sq->where('first_name', 'like', "%{$request->search}%")
                            ->orWhere('last_name', 'like', "%{$request->search}%");
                    });
            });
        }

        return Inertia::render('Finance/Invoices/Index', [
            'invoices' => $query->latest()->paginate(20)->withQueryString(),
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Finance/Invoices/Create', [
            'patients' => Patient::select('id', 'first_name', 'last_name', 'patient_uid')->orderBy('first_name')->get(),
        ]);
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

    public function show(Invoice $invoice)
    {
        $invoice->load(['patient', 'items', 'claim.payer', 'cashPayments']);

        return Inertia::render('Finance/Invoices/Show', [
            'invoice' => $invoice,
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

        // Find or use active cash shift
        $shift = CashShift::where('status', 'open')->latest()->first();

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
