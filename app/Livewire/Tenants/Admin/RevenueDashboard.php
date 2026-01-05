<?php

namespace App\Livewire\Tenants\Admin;

use App\Models\RevenueSummary;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
class RevenueDashboard extends Component
{
    use WithPagination;

    public string $timePeriod = 'month';
    public float $totalRevenue = 0.0, $medicationRevenue = 0.0, $appointmentRevenue = 0.0, $labRevenue = 0.0, $admissionRevenue = 0.0;
    public float $previousTotalRevenue = 0.0, $revenueGrowth = 0.0;

    public function mount() { $this->refreshStats(); }

    public function updatedTimePeriod() {
        $this->resetPage();
        $this->refreshStats();
    }

    private function refreshStats(): void
    {
        $tenantId = tenant('id');
        $stats = Cache::remember("rev_stats_{$tenantId}_{$this->timePeriod}", 600, function() {
            [$start, $end] = $this->getDateRange($this->timePeriod);
            $curr = $this->getAggregatedRevenue($start, $end);

            [$pStart, $pEnd] = $this->getPreviousDateRange();
            $prev = $this->getAggregatedRevenue($pStart, $pEnd);

            $currTotal = $curr->med + $curr->appt + $curr->lab + $curr->adm;
            $prevTotal = $prev->med + $prev->appt + $prev->lab + $prev->adm;

            return [
                'med' => $curr->med, 'appt' => $curr->appt, 'lab' => $curr->lab, 'adm' => $curr->adm,
                'total' => $currTotal, 'prev_total' => $prevTotal,
                'growth' => $prevTotal > 0 ? (($currTotal - $prevTotal) / $prevTotal) * 100 : ($currTotal > 0 ? 100 : 0)
            ];
        });

        $this->medicationRevenue = $stats['med'];
        $this->appointmentRevenue = $stats['appt'];
        $this->labRevenue = $stats['lab'];
        $this->admissionRevenue = $stats['adm'];
        $this->totalRevenue = $stats['total'];
        $this->previousTotalRevenue = $stats['prev_total'];
        $this->revenueGrowth = $stats['growth'];
    }

    private function getAggregatedRevenue(Carbon $start, Carbon $end): object
    {
        $data = RevenueSummary::whereBetween('transaction_date', [$start, $end])->first([
            DB::raw('SUM(medication_revenue) as med'),
            DB::raw('SUM(appointment_revenue) as appt'),
            DB::raw('SUM(lab_revenue) as lab'),
            DB::raw('SUM(admission_revenue) as adm'),
        ]);

        return (object) [
            'med' => (float)($data->med ?? 0),
            'appt' => (float)($data->appt ?? 0),
            'lab' => (float)($data->lab ?? 0),
            'adm' => (float)($data->adm ?? 0),
        ];
    }

    private function getDateRange(string $period): array
    {
        $now = Carbon::now();
        return match ($period) {
            'today' => [$now->copy()->startOfDay(), $now->endOfDay()],
            'week'  => [$now->copy()->startOfWeek(), $now->endOfWeek()],
            'year'  => [$now->copy()->startOfYear(), $now->endOfYear()],
            default => [$now->copy()->startOfMonth(), $now->endOfMonth()],
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
        $tenantId = tenant('id');
        $page = $this->getPage();

        $patientRevenues = Cache::remember("rev_list_{$tenantId}_{$this->timePeriod}_p{$page}", 300, function() {
            [$start, $end] = $this->getDateRange($this->timePeriod);
            return RevenueSummary::whereBetween('transaction_date', [$start, $end])
                ->select('patient_id',
                    DB::raw('SUM(medication_revenue) as medications'),
                    DB::raw('SUM(appointment_revenue) as appointments'),
                    DB::raw('SUM(lab_revenue) as labs'),
                    DB::raw('SUM(admission_revenue) as admissions'),
                    // DB Agnostic sum of columns for ordering
                    DB::raw('(SUM(medication_revenue) + SUM(appointment_revenue) + SUM(lab_revenue) + SUM(admission_revenue)) as total')
                )
                ->groupBy('patient_id')
                ->orderByDesc('total')
                ->with('patient')
                ->paginate(10);
        });

        return view('livewire.tenants.admin.revenue-dashboard', ['patientRevenues' => $patientRevenues]);
    }
}
