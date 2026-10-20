<?php

namespace App\Http\Controllers;

use App\Enums\CashShiftStatus;
use App\Http\Requests\CloseCashShiftRequest;
use App\Http\Requests\StoreCashPaymentRequest;
use App\Http\Requests\StoreCashShiftRequest;
use App\Models\CashPayment;
use App\Models\CashShift;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CaisseController extends Controller
{
    public function index(): Response
    {
        $activeShift = CashShift::where('status', CashShiftStatus::Open)
            ->where('user_id', auth()->id())
            ->with(['payments.patient:id,first_name,last_name', 'payments.invoice:id,invoice_number'])
            ->first();

        $todayPayments = CashPayment::whereHas('cashShift', function ($q) {
            $q->whereDate('opened_at', today());
        })
            ->with(['patient:id,first_name,last_name', 'invoice:id,invoice_number'])
            ->orderBy('created_at', 'desc')
            ->get();

        $shiftHistory = CashShift::where('user_id', auth()->id())
            ->withCount('payments')
            ->orderBy('opened_at', 'desc')
            ->limit(10)
            ->get();

        return Inertia::render('Caisse/Index', [
            'activeShift' => $activeShift,
            'todayPayments' => $todayPayments,
            'shiftHistory' => $shiftHistory,
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

        CashShift::create([
            ...$request->validated(),
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

    public function pay(StoreCashPaymentRequest $request): RedirectResponse
    {
        $shift = CashShift::where('status', CashShiftStatus::Open)
            ->where('user_id', auth()->id())
            ->first();

        if (! $shift) {
            return back()->with('error', 'No open shift found. Please open a shift first.');
        }

        $validated = $request->validated();

        // A payment must be worth something and must be traceable to a patient
        // or an invoice, otherwise the receipt and the shift total are useless.
        if ((float) $validated['amount_paid'] <= 0) {
            return back()->withErrors(['amount_paid' => 'The payment amount must be greater than zero.']);
        }

        if (empty($validated['invoice_id']) && empty($validated['patient_id'])) {
            return back()->withErrors(['patient_id' => 'Select a patient or an invoice for this payment.']);
        }

        $shift->payments()->create($validated);

        return back()->with('success', 'Payment recorded successfully.');
    }

    public function receipt(CashPayment $payment): Response
    {
        return Inertia::render('Caisse/Receipt', [
            'payment' => $payment->load([
                'cashShift.user:id,name',
                'patient:id,first_name,last_name,patient_uid',
                'invoice:id,invoice_number,total_amount',
            ]),
        ]);
    }
}
