<?php

namespace App\Services\Billing;

use App\Enums\AdmissionStatus;
use App\Enums\EmergencyStatus;
use App\Enums\InvoiceStatus;
use App\Enums\SurgeryStatus;
use App\Models\Admission;
use App\Models\EmergencyCase;
use App\Models\Expenditure;
use App\Models\Invoice;
use App\Models\Surgery;
use Illuminate\Support\Facades\DB;

class RevenueService
{
    /**
     * Get comprehensive revenue summary across all sources.
     */
    public function getRevenueSummary(string $startDate, string $endDate): array
    {
        $invoiceData = Invoice::whereBetween('invoice_date', [$startDate, $endDate])
            ->where('status', InvoiceStatus::PAID->value)
            ->selectRaw('COALESCE(SUM(total_amount), 0) as total, COUNT(*) as count')
            ->first();

        $surgeryData = Surgery::whereBetween('scheduled_at', [$startDate, $endDate])
            ->where('status', SurgeryStatus::COMPLETED->value)
            ->selectRaw('COALESCE(SUM(surgery_fee), 0) as total, COUNT(*) as count')
            ->first();

        $emergencyData = EmergencyCase::whereBetween('arrived_at', [$startDate, $endDate])
            ->whereIn('status', [EmergencyStatus::STABILIZED->value, EmergencyStatus::DISCHARGED->value])
            ->selectRaw('COALESCE(SUM(emergency_fee), 0) as total, COUNT(*) as count')
            ->first();

        $admissionsTotal = Admission::whereBetween('admission_date', [$startDate, $endDate])
            ->where('status', AdmissionStatus::Discharged->value)
            ->sum('observation_fee');

        $totalRevenue = $invoiceData->total + $surgeryData->total + $emergencyData->total + $admissionsTotal;

        return [
            'total_revenue' => $totalRevenue,
            'invoices' => (float) $invoiceData->total,
            'invoice_count' => (int) $invoiceData->count,
            'surgeries' => (float) $surgeryData->total,
            'surgery_count' => (int) $surgeryData->count,
            'emergencies' => (float) $emergencyData->total,
            'emergency_count' => (int) $emergencyData->count,
            'admissions' => (float) $admissionsTotal,
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
        $monthlyData = [];

        // Single query per table, grouped by month
        $invoiceByMonth = Invoice::whereYear('invoice_date', (string) $year)
            ->where('status', InvoiceStatus::PAID->value)
            ->selectRaw('CAST(strftime("%m", invoice_date) AS INTEGER) as month, COALESCE(SUM(total_amount), 0) as total')
            ->groupBy(DB::raw('strftime("%m", invoice_date)'))
            ->pluck('total', 'month');

        $surgeryByMonth = Surgery::whereYear('scheduled_at', (string) $year)
            ->where('status', SurgeryStatus::COMPLETED->value)
            ->selectRaw('CAST(strftime("%m", scheduled_at) AS INTEGER) as month, COALESCE(SUM(surgery_fee), 0) as total')
            ->groupBy(DB::raw('strftime("%m", scheduled_at)'))
            ->pluck('total', 'month');

        $emergencyByMonth = EmergencyCase::whereYear('arrived_at', (string) $year)
            ->whereIn('status', [EmergencyStatus::STABILIZED->value, EmergencyStatus::DISCHARGED->value])
            ->selectRaw('CAST(strftime("%m", arrived_at) AS INTEGER) as month, COALESCE(SUM(emergency_fee), 0) as total')
            ->groupBy(DB::raw('strftime("%m", arrived_at)'))
            ->pluck('total', 'month');

        $admissionByMonth = Admission::whereYear('admission_date', (string) $year)
            ->where('status', AdmissionStatus::Discharged->value)
            ->selectRaw('CAST(strftime("%m", admission_date) AS INTEGER) as month, COALESCE(SUM(observation_fee), 0) as total')
            ->groupBy(DB::raw('strftime("%m", admission_date)'))
            ->pluck('total', 'month');

        $expenseByMonth = Expenditure::whereYear('expenditure_date', (string) $year)
            ->selectRaw('CAST(strftime("%m", expenditure_date) AS INTEGER) as month, COALESCE(SUM(amount), 0) as total')
            ->groupBy(DB::raw('strftime("%m", expenditure_date)'))
            ->pluck('total', 'month');

        for ($m = 1; $m <= 12; $m++) {
            $invoice = (float) ($invoiceByMonth[$m] ?? 0);
            $surgery = (float) ($surgeryByMonth[$m] ?? 0);
            $emergency = (float) ($emergencyByMonth[$m] ?? 0);
            $admission = (float) ($admissionByMonth[$m] ?? 0);
            $expense = (float) ($expenseByMonth[$m] ?? 0);
            $totalRevenue = $invoice + $surgery + $emergency + $admission;

            $monthlyData[] = [
                'month' => date('M', mktime(0, 0, 0, $m, 1)),
                'revenue' => $totalRevenue,
                'invoices' => $invoice,
                'surgeries' => $surgery,
                'emergencies' => $emergency,
                'admissions' => $admission,
                'expenditure' => $expense,
                'profit' => $totalRevenue - $expense,
            ];
        }

        return $monthlyData;
    }

    /**
     * Get revenue breakdown by service type from invoices.
     */
    public function getDepartmentRevenue(string $startDate, string $endDate): array
    {
        return DB::table('invoices')
            ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->whereBetween('invoices.invoice_date', [$startDate, $endDate])
            ->where('invoices.status', InvoiceStatus::PAID->value)
            ->select('invoice_items.item_type', DB::raw('SUM(invoice_items.total_price) as total'))
            ->groupBy('invoice_items.item_type')
            ->get()
            ->toArray();
    }
}
