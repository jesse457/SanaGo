<?php

namespace App\Http\Controllers;

use App\Enums\CashShiftStatus;
use App\Http\Requests\CloseCashShiftRequest;
use App\Http\Requests\StoreCashPaymentRequest;
use App\Http\Requests\StoreCashShiftRequest;
use App\Http\Requests\StoreSettlementRequest;
use App\Models\CashPayment;
use App\Models\CashPoint;
use App\Models\CashShift;
use App\Models\Patient;
use App\Services\Billing\BillingService;
use App\Services\Billing\SettlementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * The cash desk.
 *
 * The counter flow is: open a shift on a till, find or register the patient,
 * show what they still owe, take the money for the selected services and print
 * a receipt. Money always settles named charges — never a free amount.
 */
class CaisseController extends Controller
{
    public function index(Request $request): Response
    {
        $activeShift = CashShift::where('status', CashShiftStatus::Open)
            ->where('user_id', auth()->id())
            ->with(['payments.patient:id,first_name,last_name', 'payments.invoice:id,invoice_number', 'cashPoint:id,name'])
            ->first();

        $todayPayments = CashPayment::whereHas('cashShift', function ($q) {
            $q->whereDate('opened_at', today());
        })
            ->with(['patient:id,first_name,last_name', 'invoice:id,invoice_number', 'cashPoint:id,name'])
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
        ]);
    }

    public function openShift(StoreCashShiftRequest $request): RedirectResponse
    {
        $existing = CashShift::where('status', CashShiftStatus::Open)
            ->where('user_id', auth()->id())
            ->exists();

        if ($existing) {
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
        $shift = CashShift::where('status', CashShiftStatus::Open)
            ->where('user_id', auth()->id())
            ->first();

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
     * What this patient still owes, plus the priced catalogue for ad-hoc sales.
     */
    public function outstanding(Patient $patient, SettlementService $settlement): JsonResponse
    {
        // The list is scoped to the cashier's till: the main caisse must not
        // offer pharmacy charges, and the pharmacy counter sees only its own.
        $shift = CashShift::where('status', CashShiftStatus::Open)
            ->where('user_id', auth()->id())
            ->with('cashPoint')
            ->first();

        return response()->json(
            $settlement->counterPayload($patient, $shift?->cashPoint),
        );
    }

    /**
     * Take the money for the selected services.
     */
    public function settle(StoreSettlementRequest $request, SettlementService $settlement): RedirectResponse
    {
        $shift = CashShift::where('status', CashShiftStatus::Open)
            ->where('user_id', auth()->id())
            ->first();

        if (! $shift) {
            return back()->with('error', 'Open a shift before taking payments.');
        }

        $validated = $request->validated();
        $patient = Patient::findOrFail($validated['patient_id']);

        $itemIds = $validated['item_ids'] ?? [];

        // Services billed on the spot are added to the running bill first, then
        // settled together with the charges the patient already had.
        if (! empty($validated['service_ids'])) {
            $itemIds = [
                ...$itemIds,
                ...$settlement->billServices(
                    $patient,
                    $validated['service_ids'],
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
        $shift = CashShift::where('status', CashShiftStatus::Open)
            ->where('user_id', auth()->id())
            ->first();

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
}
