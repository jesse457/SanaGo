<?php

namespace App\Http\Controllers;

use App\Enums\PrescriptionStatus;
use App\Http\Requests\StoreDispensationRequest;
use App\Models\Medication;
use App\Models\Patient;
use App\Services\Pharmacy\PharmacyService;
use Inertia\Inertia;

class DispensationController extends Controller
{
    public function index()
    {
        return Inertia::render('Pharmacy/Dispensation', [
            'patients' => Patient::whereHas('prescriptions', function ($q) {
                $q->whereIn('status', [PrescriptionStatus::NEW->value, PrescriptionStatus::PARTIAL->value]);
            })->with(['prescriptions' => function ($q) {
                $q->whereIn('status', [PrescriptionStatus::NEW->value, PrescriptionStatus::PARTIAL->value])->with('doctor', 'items.medication');
            }])->get(),
            'medications' => Medication::where('is_active', true)
                ->where('total_stock', '>', 0)
                ->get(),
        ]);
    }

    public function store(StoreDispensationRequest $request, PharmacyService $service)
    {
        try {
            $service->dispense($request->validated());

            return redirect()->route('dispensation.index')->with('success', 'Medication dispensed successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
