<?php

namespace App\Services\Billing;

use App\Models\Admission;
use App\Models\Expenditure;
use App\Models\Invoice;
use App\Models\Surgery;
use App\Models\EmergencyCase;
use Illuminate\Support\Facades\DB;

class RevenueService
{
    /**
     * Get comprehensive revenue summary across all sources.
     */
    public function getRevenueSummary(string $startDate, string $endDate): array
    {
        $invoices = Invoice::whereBetween('invoice_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->sum('total_amount');

        $surgeries = Surgery::whereBetween('scheduled_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('surgery_fee');

        $emergencies = EmergencyCase::whereBetween('arrived_at', [$startDate, $endDate])
            ->whereIn('status', ['stabilized', 'discharged'])
            ->sum('emergency_fee');

        $admissions = Admission::whereBetween('admission_date', [$startDate, $endDate])
            ->where('status', 'discharged')
            ->sum('observation_fee');

        $totalRevenue = $invoices + $surgeries + $emergencies + $admissions;

        // Count of invoices in period
        $invoiceCount = Invoice::whereBetween('invoice_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->count();

        $surgeryCount = Surgery::whereBetween('scheduled_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->count();

        $emergencyCount = EmergencyCase::whereBetween('arrived_at', [$startDate, $endDate])
            ->whereIn('status', ['stabilized', 'discharged'])
            ->count();

        return [
            'total_revenue' => $totalRevenue,
            'invoices' => $invoices,
            'invoice_count' => $invoiceCount,
            'surgeries' => $surgeries,
            'surgery_count' => $surgeryCount,
            'emergencies' => $emergencies,
            'emergency_count' => $emergencyCount,
            'admissions' => $admissions,
        ];
    }

    /**
     * Get expenditure breakdown by category.
     */
    public function getExpenditureSummary(string $startDate, string $endDate): array
    {
        return Expenditure::whereBetween('expenditure_date', [$startDate, $endDate])
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get()
            ->toArray();
    }

    /**
     * Get month-by-month revenue, expenditure, and profit for a year.
     */
    public function getMonthlyRevenue(int $year): array
    {
        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $start = "{$year}-{$m}-01";
            $end = date('Y-m-t', strtotime($start));

            $invoice = Invoice::whereBetween('invoice_date', [$start, $end])
                ->where('status', 'paid')->sum('total_amount');
            $surgery = Surgery::whereBetween('scheduled_at', [$start, $end])
                ->where('status', 'completed')->sum('surgery_fee');
            $emergency = EmergencyCase::whereBetween('arrived_at', [$start, $end])
                ->whereIn('status', ['stabilized', 'discharged'])->sum('emergency_fee');
            $admission = Admission::whereBetween('admission_date', [$start, $end])
                ->where('status', 'discharged')->sum('observation_fee');

            $expense = Expenditure::whereBetween('expenditure_date', [$start, $end])->sum('amount');
            $totalRevenue = $invoice + $surgery + $emergency + $admission;

            $data[] = [
                'month' => date('M', strtotime($start)),
                'revenue' => $totalRevenue,
                'invoices' => $invoice,
                'surgeries' => $surgery,
                'emergencies' => $emergency,
                'admissions' => $admission,
                'expenditure' => $expense,
                'profit' => $totalRevenue - $expense,
            ];
        }

        return $data;
    }

    /**
     * Get revenue breakdown by service type from invoices.
     */
    public function getDepartmentRevenue(string $startDate, string $endDate): array
    {
        return DB::table('invoices')
            ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->whereBetween('invoices.invoice_date', [$startDate, $endDate])
            ->where('invoices.status', 'paid')
            ->select('invoice_items.item_type', DB::raw('SUM(invoice_items.total_price) as total'))
            ->groupBy('invoice_items.item_type')
            ->get()
            ->toArray();
    }

}
