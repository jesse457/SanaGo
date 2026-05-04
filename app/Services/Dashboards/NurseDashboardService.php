<?php

namespace App\Services\Dashboards;

use App\Models\Admission;
use App\Models\Bed;
use App\Models\Patient;
use App\Models\Supply;
use App\Models\SupplyUsage;
use Carbon\Carbon;

class NurseDashboardService
{
    /**
     * Retrieve main dashboard statistics and lists for nurses.
     */
    public function getDashboardData(): array
    {
        $today = Carbon::today();

        // 1. Statistics (KPIs)
        $admitted = Admission::where('status', 'Admitted')->count();
        $vitalsDue = Supply::all()->count();
        $lowStock = Supply::whereColumn('current_stock', '<=', 'min_stock_level')->count();
        $runningIVs = SupplyUsage::whereHas('supply', fn ($q) => $q->where('name', 'like', '%IV%'))
            ->whereDate('usage_date', $today)
            ->count();

        // 2. Admitted patients for bed map
        $admittedPatients = Admission::where('status', 'Admitted')
            ->with(['patient', 'bed.ward'])
            ->orderByDesc('admission_date')
            ->get();

        // 3. Low-stock items
        $lowStockItems = Supply::whereColumn('current_stock', '<=', 'min_stock_level')
            ->get(['name', 'current_stock']);

        return [
            'admitted' => $admitted,
            'vitals_due' => $vitalsDue,
            'low_stock' => $lowStock,
            'running_ivs' => $runningIVs,
            'admitted_patients' => $admittedPatients,
            'low_stock_items' => $lowStockItems,
        ];
    }
}
