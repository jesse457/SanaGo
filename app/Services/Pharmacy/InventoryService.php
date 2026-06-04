<?php

namespace App\Services\Pharmacy;

use App\Enums\BatchStatus;
use App\Models\Batch;
use App\Models\Medication;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function createMedication(array $validated, ?UploadedFile $image = null): Medication
    {
        return DB::transaction(function () use ($validated, $image) {
            $medication = Medication::create([
                'name' => $validated['name'],
                'dosage_units' => $validated['dosage_units'],
                'min_stock_level' => $validated['min_stock_level'],
                'description' => $validated['description'] ?? null,
                'total_stock' => 0,
            ]);

            $totalStock = 0;
            foreach ($validated['batches'] ?? [] as $b) {
                $medication->batches()->create([
                    'batch_number' => $b['batch_number'],
                    'expiry_date' => $b['expiry_date'],
                    'initial_quantity' => $b['quantity'],
                    'current_quantity' => $b['quantity'],
                    'purchase_price' => $b['purchase_price'],
                    'status' => BatchStatus::ACTIVE->value,
                ]);
                $totalStock += $b['quantity'];
            }
            $medication->update(['total_stock' => $totalStock]);

            if ($image) {
                $medication->addMedia($image)->toMediaCollection('image');
            }

            return $medication;
        });
    }

    public function addBatch(Medication $medication, array $data): Batch
    {
        $batch = $medication->batches()->create([
            'batch_number' => $data['batch_number'],
            'expiry_date' => $data['expiry_date'],
            'initial_quantity' => $data['quantity'],
            'current_quantity' => $data['quantity'],
            'purchase_price' => $data['purchase_price'] ?? 0,
            'status' => BatchStatus::ACTIVE->value,
        ]);
        $medication->increment('total_stock', $data['quantity']);

        return $batch;
    }

    public function depleteBatch(Batch $batch): void
    {
        $batch->update([
            'current_quantity' => 0,
            'status' => BatchStatus::DEPLETED->value,
        ]);
        $medication = $batch->batchable;
        $medication->update(['total_stock' => $medication->batches()->sum('current_quantity')]);
    }
}
