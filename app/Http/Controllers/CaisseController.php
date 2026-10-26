<?php

namespace App\Http\Controllers;

use App\Enums\CashShiftStatus;
use App\Enums\PlanFeature;
use App\Http\Requests\CloseCashShiftRequest;
use App\Http\Requests\StoreCashPaymentRequest;
use App\Http\Requests\StoreCashShiftRequest;
use App\Http\Requests\StoreChargeRequest;
use App\Http\Requests\StoreSettlementRequest;
use App\Http\Requests\UpdateChargeRequest;
use App\Models\CashPayment;
use App\Models\CashPoint;
use App\Models\CashShift;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Services\Billing\BillingService;
use App\Services\Billing\SettlementService;
use App\Support\PlatformContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * The cash desk — bills and money in one screen.
 *
 * There is no separate invoice module: the bill a patient owes, the charges
 * written on it (catalogue services, module charges, and what was consumed
 * during the stay), the payments against it and the receipt all live here.
 *
 * The counter flow is: open a shift on a till, find or register the patient,
 * read the bill, add what they consumed, take the money for the selected lines
 * and print a receipt. Money always settles named charges — never a free
 * amount.
 */
class CaisseController extends Controller
{
    public function index(Request $request, SettlementService $settlement): Response
    {
        $activeShift = $this->activeShift();
        $billFilters = $request->only(['search', 'status']);

        $todayPayments = CashPayment::whereHas('cashShift', function ($q) {
            $q->whereBetween('opened_at', [today()->startOfDay(), today()->endOfDay()]);
        })
            ->with(['patient:id,first_name,last_name', 'invoice:id,invoice_number'])
            ->orderBy('created_at', 'desc')
            ->get();

        $shiftHistory = CashShift::where('user_id', auth()->id())
            ->with('cashPoint:id,name')
            ->withCount('payments')
            ->orderBy('opened_at', 'desc')
            ->limit(10)
            ->get();

        // A patient registered from the desk comes back through this prop.
        $selectedPatient = null;

        if ($request->filled('patient_id')) {
            $patient = Patient::find($request->integer('patient_id'));

            if ($patient !== null) {
                $selectedPatient = [
                    'id' => $patient->id,
                    'first_name' => $patient->first_name,
                    'last_name' => $patient->last_name,
                    'patient_uid' => $patient->patient_uid,
                    'phone' => $patient->phone,
                    'gender' => $patient->gender?->value,
                ];
            }
        }

        return Inertia::render('Caisse/Index', [
            'activeShift' => $activeShift,
            'todayPayments' => $todayPayments,
            'shiftHistory' => $shiftHistory,
            'cashPoints' => CashPoint::options(),
            'defaultCashPointId' => CashPoint::primary()->id,
            'selectedPatient' => $selectedPatient,
            // Deep link from the bills list: open that patient's bill.
            'focusInvoiceId' => $request->integer('invoice_id') ?: null,
            'bills' => $settlement->billList($billFilters),
            'billFilters' => $billFilters,
        ]);
    }

    /**
     * The till ledger: every shift opened on every cash point, with its
     * counted balance and variance. A separate screen from the desk so
     * reconciling the money never requires walking through the counter.
     */
    public function shifts(Request $request): Response
    {
        $status = (string) $request->string('status', 'all');

        $shifts = CashShift::query()
            ->with(['user:id,name', 'cashPoint:id,name,module'])
            ->withCount('payments')
            ->withSum('payments as collected', 'amount_paid')
            ->when(in_array($status, ['open', 'closed'], true), fn ($query) => $query->where('status', $status))
            ->orderByDesc('opened_at')
            ->paginate(20)
            ->withQueryString()
            ->through(fn (CashShift $shift): array => [
                'id' => $shift->id,
                'payment_point' => $shift->payment_point,
                'cashier' => $shift->user?->name ?? '—',
                'cash_point' => $shift->cashPoint?->name ?? $shift->payment_point,
                'status' => $shift->status->value,
                'opened_at' => $shift->opened_at?->format('Y-m-d H:i'),
                'closed_at' => $shift->closed_at?->format('Y-m-d H:i'),
                'opening_balance' => (float) $shift->opening_balance,
                'closing_balance' => $shift->closing_balance === null ? null : (float) $shift->closing_balance,
                'expected_total' => $shift->expected_total === null ? null : (float) $shift->expected_total,
                'variance' => $shift->variance === null ? null : (float) $shift->variance,
                'payments_count' => (int) $shift->payments_count,
                'collected' => (float) ($shift->collected ?? 0),
            ]);

        return Inertia::render('Caisse/Shifts', [
            'shifts' => $shifts,
            'filters' => ['status' => $status],
        ]);
    }

    public function openShift(StoreCashShiftRequest $request): RedirectResponse
    {
        if ($this->activeShift() !== null) {
            return back()->with('error', 'You already have an open shift. Close it before opening a new one.');
        }

        $cashPoint = CashPoint::findOrFail($request->validated('cash_point_id'));

        CashShift::create([
            'cash_point_id' => $cashPoint->id,
            'payment_point' => $cashPoint->name,
            'opening_balance' => $request->validated('opening_balance'),
            'user_id' => auth()->id(),
            'opened_at' => now(),
            'status' => CashShiftStatus::Open->value,
        ]);

        return back()->with('success', 'Shift opened successfully.');
    }

    public function closeShift(CloseCashShiftRequest $request): RedirectResponse
    {
        $shift = $this->activeShift();

        if (! $shift) {
            return back()->with('error', 'No open shift found.');
        }

        $totalPayments = $shift->payments()->sum('amount_paid');
        $expectedTotal = (float) $shift->opening_balance + (float) $totalPayments;

        $counted = $request->validated('closing_balance');
        $closingBalance = $counted === null ? $expectedTotal : (float) $counted;

        $shift->update([
            'status' => CashShiftStatus::Closed->value,
            'closed_at' => now(),
            'expected_total' => $expectedTotal,
            'closing_balance' => $closingBalance,
            'variance' => $closingBalance - $expectedTotal,
        ]);

        return back()->with('success', 'Shift closed successfully.');
    }

    /**
     * Patient search for the counter (name, uid or phone).
     */
    public function searchPatients(Request $request): JsonResponse
    {
        $search = trim((string) $request->string('search'));

        if (mb_strlen($search) < 2) {
            return response()->json(['patients' => []]);
        }

        $patients = Patient::query()
            ->where(function ($query) use ($search): void {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('patient_uid', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            })
            ->orderBy('last_name')
            ->limit(10)
            ->get(['id', 'first_name', 'last_name', 'patient_uid', 'phone', 'gender']);

        return response()->json([
            'patients' => $patients->map(fn (Patient $patient): array => [
                'id' => $patient->id,
                'first_name' => $patient->first_name,
                'last_name' => $patient->last_name,
                'patient_uid' => $patient->patient_uid,
                'phone' => $patient->phone,
                'gender' => $patient->gender?->value,
            ])->all(),
        ]);
    }

    /**
     * What this patient owes and what is already on their bill, plus the
     * priced catalogue and the headings for hand-written charges.
     */
    public function outstanding(Request $request, Patient $patient, SettlementService $settlement): JsonResponse
    {
        // The list is scoped to the cashier's till: the main caisse must not
        // offer pharmacy charges, and the pharmacy counter sees only its own.
        $shift = $this->activeShift();

        return response()->json([
            ...$settlement->counterPayload($patient, $shift?->cashPoint),
            'claim_context' => $settlement->claimContext(
                $patient,
                $this->canReachClaims($request),
            ),
        ]);
    }

    /**
     * Write hand-written charges (what the patient consumed during the stay)
     * onto their running bill.
     */
    public function storeCharge(StoreChargeRequest $request, SettlementService $settlement): RedirectResponse
    {
        $validated = $request->validated();
        $patient = Patient::findOrFail($validated['patient_id']);

        $settlement->addCharges(
            $patient,
            $validated['lines'],
            $this->activeShift()?->cashPoint,
        );

        return back()->with('success', 'Charge added to the bill.');
    }

    /**
     * Correct a hand-written charge before it is paid.
     */
    public function updateCharge(UpdateChargeRequest $request, InvoiceItem $item, SettlementService $settlement): RedirectResponse
    {
        $settlement->updateCharge($item, $request->validated());

        return back()->with('success', 'Charge updated.');
    }

    /**
     * Take a hand-written charge back off the bill.
     */
    public function destroyCharge(InvoiceItem $item, SettlementService $settlement): RedirectResponse
    {
        $settlement->voidCharge($item);

        return back()->with('success', 'Charge removed from the bill.');
    }

    /**
     * Take the money for the selected services.
     */
    public function settle(StoreSettlementRequest $request, SettlementService $settlement): RedirectResponse
    {
        $shift = $this->activeShift();

        if (! $shift) {
            return back()->with('error', 'Open a shift before taking payments.');
        }

        $validated = $request->validated();
        $patient = Patient::findOrFail($validated['patient_id']);

        $itemIds = $validated['item_ids'] ?? [];

        // Services billed on the spot are added to the running bill first, then
        // settled together with the charges the patient already had. A pick
        // names the record behind the line (a drug, a lab test, a bed day of
        // the open stay, a price-list service) so the price always comes from
        // the catalogue.
        if (! empty($validated['picks'])) {
            $itemIds = [
                ...$itemIds,
                ...$settlement->billPicks(
                    $patient,
                    $validated['picks'],
                    $shift->cashPoint,
                ),
            ];
        }

        if ($itemIds === []) {
            return back()->withErrors([
                'item_ids' => 'Select at least one service to pay for.',
            ]);
        }

        $payment = $settlement->settle(
            $patient,
            $itemIds,
            $shift,
            $validated['payment_method'],
            (float) $validated['amount_tendered'],
            $validated['client_uuid'] ?? null,
        );

        return redirect()
            ->route('caisse.receipt', $payment)
            ->with('success', 'Payment recorded and receipt issued.');
    }

    /**
     * Legacy quick payment: still accepted, but now always attached to the
     * patient's running bill so no franc is left floating.
     */
    public function pay(StoreCashPaymentRequest $request, BillingService $billing): RedirectResponse
    {
        $shift = $this->activeShift();

        if (! $shift) {
            return back()->with('error', 'No open shift found. Please open a shift first.');
        }

        $validated = $request->validated();

        if ((float) $validated['amount_paid'] <= 0) {
            return back()->withErrors(['amount_paid' => 'The payment amount must be greater than zero.']);
        }

        if (empty($validated['invoice_id']) && empty($validated['patient_id'])) {
            return back()->withErrors(['patient_id' => 'Select a patient or an invoice for this payment.']);
        }

        $invoiceId = $validated['invoice_id'] ?? null;

        if ($invoiceId === null) {
            $invoiceId = $billing->findOrCreateDraftInvoice((int) $validated['patient_id'])->id;
        }

        $shift->payments()->create([
            ...$validated,
            'invoice_id' => $invoiceId,
            'cash_point_id' => $shift->cash_point_id,
        ]);

        return back()->with('success', 'Payment recorded successfully.');
    }

    public function receipt(CashPayment $payment): Response
    {
        return Inertia::render('Caisse/Receipt', [
            'payment' => $payment->load([
                'cashShift.user:id,name',
                'cashShift.cashPoint:id,name,receipt_prefix',
                'patient:id,first_name,last_name,patient_uid',
                'invoice:id,invoice_number,total_amount',
            ]),
        ]);
    }

    /**
     * The till the signed-in cashier is currently working.
     *
     * Owned by the cashier, never looked up by id: money must land on the shift
     * that took it, not on somebody else's.
     */
    private function activeShift(): ?CashShift
    {
        return CashShift::where('status', CashShiftStatus::Open)
            ->where('user_id', auth()->id())
            ->with('cashPoint')
            ->latest()
            ->first();
    }

    /**
     * Whether this cashier may start an insurance claim from a bill: it needs
     * the finance permission and the plan's tiers payant capability.
     */
    private function canReachClaims(Request $request): bool
    {
        if (! $request->user()?->can('view-finance')) {
            return false;
        }

        $tenant = tenant();

        return $tenant === null
            || PlatformContext::featureEnabled($tenant, PlanFeature::InsuranceClaims);
    }
}
