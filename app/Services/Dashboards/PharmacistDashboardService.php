<?php

namespace App\Services\Dashboards;

use App\Models\Dispensation;
use App\Models\Medication;
use App\Models\Prescription;
use Carbon\Carbon;

class PharmacistDashboardService
{
    /**
     * Retrieve main dashboard statistics and lists for pharmacists.
     */
    public function getDashboardData(): array
    {
        $today = Carbon::today();

        // 1. Statistics
        $prescriptionsDispensedToday = Prescription::whereHas('items.dispensations', function ($query) use ($today) {
            $query->whereDate('created_at', $today);
        })->count();

        $prescriptionsPending = Prescription::where('status', 'prescribed')
            ->whereHas('items', function ($query) {
                $query->whereColumn('quantity_prescribed', '>', 'dispensed_quantity');
            })->count();

        $drugsLeftInInventory = Medication::sum('stock_quantity');

        // 2. Medications list with search and pagination support
        $medications = Medication::query();

        return [
            'prescriptions_dispensed_today' => $prescriptionsDispensedToday,
            'prescriptions_pending' => $prescriptionsPending,
            'drugs_left_in_inventory' => $drugsLeftInInventory,
        ];
    }

    /**
     * Get medications with search and pagination.
     */
    public function getMedications($search = '', $sortBy = 'name', $sortDirection = 'asc', $perPage = 10)
    {
        $query = Medication::query();

        if ($search) {
            $query->where('name', 'ILIKE', "%{$search}%");
        }

        $query->orderBy($sortBy, $sortDirection);

        return $query->paginate($perPage);
    }
}
