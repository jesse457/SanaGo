<?php

namespace App\Http\Controllers;

use App\Enums\PrescriptionStatus;
use App\Http\Requests\StorePrescriptionRequest;
use App\Models\Medication;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PrescriptionController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Prescriptions/Index', [
            'prescriptions' => Prescription::with(['patient:id,first_name,last_name,patient_uid', 'doctor:id,name'])
                ->withCount('items')
                ->orderBy('prescription_date', 'desc')
                ->paginate(20),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Prescriptions/Create', [
            'patients' => Patient::select('id', 'first_name', 'last_name', 'patient_uid', 'date_of_birth', 'gender')
                ->orderBy('last_name')
                ->get(),
            'medications' => Medication::select('id', 'name', 'strength', 'dosage_units', 'unit_cost')
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(StorePrescriptionRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            $prescription = Prescription::create([
                'patient_id' => $validated['patient_id'],
                'doctor_id' => auth()->id(),
                'prescription_date' => now(),
                'status' => PrescriptionStatus::PARTIAL->value,
                'notes' => $validated['notes'] ?? null,
                'total_amount' => 0,
            ]);

            $totalAmount = 0;

            foreach ($validated['items'] as $item) {
                $medication = Medication::find($item['medication_id']);
                $unitPrice = $medication->unit_cost ?? 0;
                $itemTotal = $unitPrice * $item['quantity'];

                PrescriptionItem::create([
                    'prescription_id' => $prescription->id,
                    'medication_id' => $item['medication_id'],
                    'drug_name' => $medication->name,
                    'dosage' => $item['dosage'],
                    'frequency' => $item['frequency'],
                    'duration' => $item['duration'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'instructions' => $item['instructions'] ?? null,
                ]);

                $totalAmount += $itemTotal;
            }

            $prescription->update(['total_amount' => $totalAmount]);
        });

        return redirect()->route('prescriptions.index')
            ->with('success', 'Prescription created successfully.');
    }

    public function show(Prescription $prescription): Response
    {
        return Inertia::render('Prescriptions/Show', [
            'prescription' => $prescription->load([
                'patient:id,first_name,last_name,patient_uid,date_of_birth,gender',
                'doctor:id,name',
                'items.medication:id,name,strength,dosage_units',
            ]),
        ]);
    }

    public function destroy(Prescription $prescription): RedirectResponse
    {
        $prescription->delete();

        return redirect()->route('prescriptions.index')
            ->with('success', 'Prescription cancelled.');
    }

    public function dispense(Prescription $prescription): RedirectResponse
    {
        if ($prescription->status === PrescriptionStatus::COMPLETED) {
            return back()->with('error', 'Prescription already dispensed.');
        }

        DB::transaction(function () use ($prescription) {
            $dispensation = $prescription->dispensations()->create([
                'patient_id' => $prescription->patient_id,
                'dispensed_by' => auth()->id(),
                'dispensed_at' => now(),
                'total_amount' => $prescription->total_amount,
            ]);

            foreach ($prescription->items as $item) {
                $dispensation->items()->create([
                    'medication_id' => $item->medication_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                ]);
            }

            $prescription->update(['status' => PrescriptionStatus::COMPLETED->value]);
        });

        return redirect()->route('prescriptions.index')
            ->with('success', 'Prescription dispensed successfully.');
    }
}
