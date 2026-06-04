<?php

namespace App\Services\Pharmacy;

use App\Enums\PrescriptionStatus;
use App\Models\Dispensation;
use App\Models\Medication;
use App\Models\Prescription;
use Illuminate\Support\Facades\DB;

class PharmacyService
{
    public function dispense(array $validated): Dispensation
    {
        return DB::transaction(function () use ($validated) {
            $totalAmount = 0;

            foreach ($validated['items'] as $item) {
                $medication = Medication::lockForUpdate()->find($item['medication_id']);
                if ($medication->total_stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$medication->name}");
                }
                $totalAmount += $medication->unit_cost * $item['quantity'];
            }

            $dispensation = Dispensation::create([
                'patient_id' => $validated['patient_id'],
                'prescription_id' => $validated['prescription_id'],
                'dispensed_by' => auth()->id(),
                'dispensed_at' => now(),
                'total_amount' => $totalAmount,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $medication = Medication::find($item['medication_id']);
                $dispensation->items()->create([
                    'medication_id' => $item['medication_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $medication->unit_cost,
                ]);
                $medication->decrement('total_stock', $item['quantity']);
            }

            if ($validated['prescription_id']) {
                Prescription::find($validated['prescription_id'])
                    ->update(['status' => PrescriptionStatus::COMPLETED->value]);
            }

            return $dispensation;
        });
    }
}
