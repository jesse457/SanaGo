<?php

namespace App\Services;

use App\Models\Dispensation;
use App\Models\Medication;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PharmacyService
{
    /**
     * Build the query for medication inventory with filters.
     *
     * @param array $filters
     * @return Builder
     */
    public function getInventoryQuery(array $filters): Builder
    {
        $query = Medication::query();

        // 1. Handle Search (Encrypted Fields)
        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];

            // Note: CipherSweet with standard Lowercase/BlindIndex only supports
            // exact matching of the hashed term, not partial (LIKE %...%).
            $query->where(function ($q) use ($searchTerm) {
                $q->whereBlind('name', 'name_index', $searchTerm);
            });
        }

        // 2. Handle Status Filters (Business Logic)
        if (!empty($filters['status'])) {
            switch ($filters['status']) {
                case 'low_stock':
                    $query->whereColumn('stock_quantity', '<=', 'min_stock_level')
                          ->where('stock_quantity', '>', 0);
                    break;
                case 'out_of_stock':
                    $query->where('stock_quantity', '<=', 0);
                    break;
                case 'available':
                    $query->where('stock_quantity', '>', 0);
                    break;
            }
        }

        return $query->latest();
    }

    /**
     * Create a new medication.
     *
     * @param array $data
     * @return Medication
     */
    public function createMedication(array $data): Medication
    {
        // Map controller input to database columns
        $attributes = [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'stock_quantity' => $data['stock_quantity'],
            'min_stock_level' => $data['min_stock_level'],
            'unit_price_purchase' => $data['unit_price'], // Mapping input to DB column
        ];

        return Medication::create($attributes);
    }

    /**
     * Update an existing medication.
     *
     * @param int $id
     * @param array $data
     * @return Medication
     */
    public function updateMedication(int $id, array $data): Medication
    {
        $medication = Medication::findOrFail($id);

        $attributes = [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'stock_quantity' => $data['stock_quantity'],
            'min_stock_level' => $data['min_stock_level'],
            // Check which key is passed, controller validation allows unit_price_purchase here
            'unit_price_purchase' => $data['unit_price_purchase'] ?? $medication->unit_price_purchase,
        ];

        $medication->update($attributes);

        return $medication;
    }

    /**
     * Delete a medication.
     *
     * @param int $id
     * @return void
     */
    public function deleteMedication(int $id): void
    {
        $medication = Medication::findOrFail($id);

        // Optional: Check if used in prescriptions before deleting
        if ($medication->prescriptionItems()->exists()) {
            throw new \Exception("Cannot delete medication linked to existing prescriptions.");
        }

        $medication->delete();
    }

    /**
     * Build the query for patients with search.
     *
     * @param array $filters
     * @return Builder
     */
    public function getPatientsQuery(array $filters): Builder
    {
        $query = Patient::query();

        if (!empty($filters['search'])) {
            $search = $filters['search'];

            // Search across multiple encrypted blind indexes
            $query->where(function ($q) use ($search) {
                $q->whereBlind('first_name', 'first_name_index', $search)
                  ->orWhereBlind('last_name', 'last_name_index', $search)
                  ->orWhereBlind('email', 'email_index', $search)
                  ->orWhereBlind('phone', 'phone_index', $search);
            });
        }

        return $query->latest();
    }

    /**
     * Process the dispensing of items from a prescription.
     *
     * @param Prescription $prescription
     * @param array $itemsData Payload containing model instances and qty to dispense
     * @param int $pharmacistId
     * @return void
     * @throws \Exception
     */
    public function dispenseItems(Prescription $prescription, array $itemsData, int $pharmacistId): void
    {
        DB::connection('pgsql_transaction')->transaction(function () use ($prescription, $itemsData, $pharmacistId) {

            foreach ($itemsData as $itemData) {
                /** @var PrescriptionItem $prescriptionItem */
                $prescriptionItem = $itemData['model'];
                $quantityToDispense = (int) $itemData['quantity'];

                // 1. Validate Amounts
                $alreadyDispensed = $prescriptionItem->dispensed_quantity ?? 0;
                $remainingAllowed = $prescriptionItem->quantity_prescribed - $alreadyDispensed;

                if ($quantityToDispense > $remainingAllowed) {
                    throw new \Exception("Cannot dispense {$quantityToDispense}. Only {$remainingAllowed} remaining for item ID: {$prescriptionItem->id}");
                }

                // 2. Lock and Check Medication Stock
                $medication = Medication::lockForUpdate()->find($prescriptionItem->medication_id);

                if (!$medication) {
                    throw new \Exception("Medication not found for item ID: {$prescriptionItem->id}");
                }

                if ($medication->stock_quantity < $quantityToDispense) {
                    throw new \Exception("Insufficient stock for {$medication->name}. Available: {$medication->stock_quantity}");
                }

                // 3. Deduct Stock
                $medication->decrement('stock_quantity', $quantityToDispense);

                // 4. Create Dispensation Record
                // Assuming sales price logic here. If simply purchase price, use unit_price_purchase.
                // If you have a markup, apply it here.
                $unitPrice = $medication->unit_price_purchase;
                $totalPrice = $unitPrice * $quantityToDispense;

                Dispensation::create([
                    'prescription_item_id' => $prescriptionItem->id,
                    'pharmacist_id'        => $pharmacistId,
                    'quantity_issued'      => $quantityToDispense,
                    'total_price'          => $totalPrice,
                    'dispensed_at'         => now(),
                    'batch_number'         => 'BATCH-' . now()->timestamp, // Logic for batching if needed
                    // 'tenant_id' handled automatically by Trait usually, if not, add: tenant('id')
                ]);

                // 5. Update Prescription Item
                $prescriptionItem->increment('dispensed_quantity', $quantityToDispense);
            }

            // 6. Update Overall Prescription Status
            $this->updatePrescriptionStatus($prescription);
        });
    }

    /**
     * Check if prescription is fully filled and update status.
     *
     * @param Prescription $prescription
     */
    protected function updatePrescriptionStatus(Prescription $prescription): void
    {
        $prescription->refresh();
        $allItems = $prescription->items;

        $allFullyDispensed = $allItems->every(function ($item) {
            return $item->dispensed_quantity >= $item->quantity_prescribed;
        });

        $anyDispensed = $allItems->contains(function ($item) {
            return $item->dispensed_quantity > 0;
        });

        if ($allFullyDispensed) {
            $prescription->update(['status' => 'completed']);
        } elseif ($anyDispensed) {
            $prescription->update(['status' => 'partial']);
        }
    }
}
