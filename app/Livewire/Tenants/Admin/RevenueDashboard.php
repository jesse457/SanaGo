<?php

namespace App\Livewire\Tenants\Admin;

use App\Models\RevenueSummary;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
class RevenueDashboard extends Component
{
    use WithPagination;

    // Filter
    public string $timePeriod = 'month';

    // Totals
    public float $totalRevenue = 0.0;
    public float $medicationRevenue = 0.0;
    public float $appointmentRevenue = 0.0;
    public float $labRevenue = 0.0;
    public float $admissionRevenue = 0.0;

    // Comparison Stats
    public float $previousTotalRevenue = 0.0;
    public float $revenueGrowth = 0.0;

    public function mount(): void
    {
        $this->refreshStats();
    }

    public function updatingTimePeriod(): void
    {
        $this->resetPage();
    }

    public function updatedTimePeriod(): void
    {
        $this->refreshStats();
    }

    private function refreshStats(): void
    {
        // 1. Current Period Stats
        [$start, $end] = $this->getDateRange($this->timePeriod);
        $currentStats = $this->getAggregatedRevenue($start, $end);

        $this->medicationRevenue = $currentStats->total_medication;
        $this->appointmentRevenue = $currentStats->total_appointment;
        $this->labRevenue = $currentStats->total_lab;
        $this->admissionRevenue = $currentStats->total_admission;

        $this->totalRevenue = $this->sumRevenueComponents($currentStats);

        // 2. Previous Period Stats (for Growth Calculation)
        [$prevStart, $prevEnd] = $this->getPreviousDateRange();
        $prevStats = $this->getAggregatedRevenue($prevStart, $prevEnd);
        $this->previousTotalRevenue = $this->sumRevenueComponents($prevStats);

        // 3. Calculate Growth %
        $this->calculateGrowth();
    }

    private function getAggregatedRevenue(Carbon $start, Carbon $end): object
    {
        /** @var object $result */
        $result = RevenueSummary::query()
            ->whereBetween('transaction_date', [$start, $end])
            ->select(
                DB::raw('COALESCE(SUM(medication_revenue), 0) as total_medication'),
                DB::raw('COALESCE(SUM(appointment_revenue), 0) as total_appointment'),
                DB::raw('COALESCE(SUM(lab_revenue), 0) as total_lab'),
                DB::raw('COALESCE(SUM(admission_revenue), 0) as total_admission')
            )
            ->first();

        // Ensure we return floats, not strings (SQL SUM often returns strings)
        return (object) [
            'total_medication'  => (float) $result->total_medication,
            'total_appointment' => (float) $result->total_appointment,
            'total_lab'         => (float) $result->total_lab,
            'total_admission'   => (float) $result->total_admission,
        ];
    }

    private function sumRevenueComponents(object $stats): float
    {
        return $stats->total_medication +
               $stats->total_appointment +
               $stats->total_lab +
               $stats->total_admission;
    }

    private function calculateGrowth(): void
    {
        if ($this->previousTotalRevenue == 0) {
            // If previous revenue was 0, growth is 100% if current > 0, else 0%
            $this->revenueGrowth = $this->totalRevenue > 0 ? 100.0 : 0.0;
        } else {
            // (Current - Previous) / Previous * 100
            $this->revenueGrowth = (($this->totalRevenue - $this->previousTotalRevenue) / $this->previousTotalRevenue) * 100;
        }
    }

    private function getDateRange(string $period): array
    {
        $now = Carbon::now();

        return match ($period) {
            'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'week'  => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'year'  => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            default => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()], // 'month'
        };
    }

    private function getPreviousDateRange(): array
    {
        $now = Carbon::now();

        return match ($this->timePeriod) {
            'today' => [$now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay()],
            'week'  => [$now->copy()->subWeek()->startOfWeek(), $now->copy()->subWeek()->endOfWeek()],
            'year'  => [$now->copy()->subYear()->startOfYear(), $now->copy()->subYear()->endOfYear()],
            default => [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()],
        };
    }

    public function render()
    {
        [$start, $end] = $this->getDateRange($this->timePeriod);

        $patientRevenues = RevenueSummary::query()
            ->whereBetween('transaction_date', [$start, $end])
            ->select(
                'patient_id',
                DB::raw('SUM(medication_revenue) as medications'),
                DB::raw('SUM(appointment_revenue) as appointments'),
                DB::raw('SUM(lab_revenue) as labs'),
                DB::raw('SUM(admission_revenue) as admissions'),
                // SQL Calculation for row total to enable sorting
                DB::raw('(SUM(medication_revenue) + SUM(appointment_revenue) + SUM(lab_revenue) + SUM(admission_revenue)) as total')
            )
            ->groupBy('patient_id')
            ->orderByDesc('total') // Sort by the calculated total
            ->with('patient') // Eager load for name decryption
            ->paginate(10);

        return view('livewire.tenants.admin.revenue-dashboard', [
            'patientRevenues' => $patientRevenues,
        ]);
    }
}
