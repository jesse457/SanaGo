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

    public string $timePeriod = 'month';

    // Aggregate properties (Typed as floats)
    public float $totalRevenue = 0.0;

    public float $medicationRevenue = 0.0;

    public float $appointmentRevenue = 0.0;

    public float $labRevenue = 0.0;

    public float $admissionRevenue = 0.0;

    public float $bedFeeRevenue = 0.0;

    public float $previousTotalRevenue = 0.0;

    public float $revenueGrowth = 0.0;

    public function mount(): void
    {
        $this->calculateAllRevenue();
        $this->calculatePreviousPeriodRevenue();
    }

    public function updatingTimePeriod(): void
    {
        $this->resetPage();
    }

    public function updatedTimePeriod(): void
    {
        $this->calculateAllRevenue();
        $this->calculatePreviousPeriodRevenue();
    }

    public function calculateAllRevenue(): void
    {
        [$start, $end] = $this->getDateRange();

        /** @var object|null $totals */
        $totals = RevenueSummary::whereBetween('transaction_date', [$start, $end])
            ->select(
                DB::raw('COALESCE(SUM(medication_revenue), 0) as total_medication'),
                DB::raw('COALESCE(SUM(appointment_revenue), 0) as total_appointment'),
                DB::raw('COALESCE(SUM(lab_revenue), 0) as total_lab'),
                DB::raw('COALESCE(SUM(admission_revenue), 0) as total_admission'),
                DB::raw('COALESCE(SUM(bed_fee_revenue), 0) as total_bed_fee')
            )->first();

        if ($totals) {
            $this->medicationRevenue = (float) $totals->total_medication;
            $this->appointmentRevenue = (float) $totals->total_appointment;
            $this->labRevenue = (float) $totals->total_lab;
            $this->admissionRevenue = (float) $totals->total_admission;
            $this->bedFeeRevenue = (float) $totals->total_bed_fee;
        }

        $this->totalRevenue = $this->medicationRevenue +
                              $this->appointmentRevenue +
                              $this->labRevenue +
                              $this->admissionRevenue +
                              $this->bedFeeRevenue;
    }

    public function calculatePreviousPeriodRevenue(): void
    {
        [$start, $end] = $this->getPreviousDateRange();

        /** @var object|null $totals */
        $totals = RevenueSummary::whereBetween('transaction_date', [$start, $end])
            ->select(
                DB::raw('COALESCE(SUM(medication_revenue), 0) as total_medication'),
                DB::raw('COALESCE(SUM(appointment_revenue), 0) as total_appointment'),
                DB::raw('COALESCE(SUM(lab_revenue), 0) as total_lab'),
                DB::raw('COALESCE(SUM(admission_revenue), 0) as total_admission'),
                DB::raw('COALESCE(SUM(bed_fee_revenue), 0) as total_bed_fee')
            )->first();

        if ($totals) {
            $this->previousTotalRevenue = (float) (
                $totals->total_medication +
                $totals->total_appointment +
                $totals->total_lab +
                $totals->total_admission +
                $totals->total_bed_fee
            );
        } else {
            $this->previousTotalRevenue = 0.0;
        }

        if ($this->previousTotalRevenue > 0) {
            $this->revenueGrowth = (($this->totalRevenue - $this->previousTotalRevenue) / $this->previousTotalRevenue) * 100;
        } else {
            $this->revenueGrowth = $this->totalRevenue > 0 ? 100.0 : 0.0;
        }
    }

    /**
     * @return array<int, Carbon>
     */
    private function getDateRange(): array
    {
        $now = Carbon::now();

        return match ($this->timePeriod) {
            'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'week' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'year' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            default => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
        };
    }

    /**
     * @return array<int, Carbon>
     */
    private function getPreviousDateRange(): array
    {
        $now = Carbon::now();

        return match ($this->timePeriod) {
            'today' => [$now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay()],
            'week' => [$now->copy()->subWeek()->startOfWeek(), $now->copy()->subWeek()->endOfWeek()],
            'year' => [$now->copy()->subYear()->startOfYear(), $now->copy()->subYear()->endOfYear()],
            default => [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()],
        };
    }

    public function render()
    {
        [$start, $end] = $this->getDateRange();

        // NOTE: We do NOT use a join here because your Patient model uses encryption.
        // A SQL Join would retrieve encrypted ciphertext for the name.
        // Instead, we group by patient_id and eager load the 'patient' relationship.
        // This allows Laravel/CipherSweet to decrypt the name when you access $row->patient->name in the view.

        $patientRevenues = RevenueSummary::query()
            ->whereBetween('transaction_date', [$start, $end])
            ->select(
                'patient_id',
                DB::raw('SUM(medication_revenue) as medications'),
                DB::raw('SUM(appointment_revenue) as appointments'),
                DB::raw('SUM(lab_revenue) as labs'),
                DB::raw('SUM(admission_revenue) as admissions'),
                DB::raw('SUM(bed_fee_revenue) as bed_fees'),
                DB::raw('SUM(medication_revenue + appointment_revenue + lab_revenue + admission_revenue + bed_fee_revenue) as total')
            )
            ->groupBy('patient_id')
            ->orderByDesc('total')
            ->with('patient') // This triggers the decryption in the model
            ->paginate(10);

        return view('livewire.tenants.admin.revenue-dashboard', [
            'patientRevenues' => $patientRevenues,
        ]);
    }
}
