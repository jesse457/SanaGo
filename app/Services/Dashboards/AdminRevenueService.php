<?php

namespace App\Services\Dashboards;

use App\Models\RevenueSummary;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminRevenueService
{
    /**
     * Get aggregated revenue stats for a specific period.
     */
    public function getRevenueStats(string $timePeriod): array
    {
        $tenantId = tenant('id');

        return Cache::remember("rev_stats_{$tenantId}_{$timePeriod}", 600, function () use ($timePeriod) {
            [$start, $end] = $this->getDateRange($timePeriod);
            $curr = $this->getAggregatedRevenue($start, $end);

            [$pStart, $pEnd] = $this->getPreviousDateRange($timePeriod);
            $prev = $this->getAggregatedRevenue($pStart, $pEnd);

            $currTotal = $curr->med + $curr->appt + $curr->lab + $curr->adm;
            $prevTotal = $prev->med + $prev->appt + $prev->lab + $prev->adm;

            return [
                'breakdown' => [
                    'medication' => $curr->med,
                    'appointment' => $curr->appt,
                    'lab' => $curr->lab,
                    'admission' => $curr->adm,
                ],
                'total_revenue' => $currTotal,
                'previous_total_revenue' => $prevTotal,
                'growth_percentage' => $prevTotal > 0
                    ? (($currTotal - $prevTotal) / $prevTotal) * 100
                    : ($currTotal > 0 ? 100 : 0),
            ];
        });
    }

    /**
     * Get paginated patient revenue list.
     */
    public function getPatientRevenueList(string $timePeriod, int $perPage = 10): LengthAwarePaginator
    {
        $tenantId = tenant('id');
        $page = request()->get('page', 1);

        return Cache::remember("rev_list_{$tenantId}_{$timePeriod}_p{$page}", 300, function () use ($timePeriod, $perPage) {
            [$start, $end] = $this->getDateRange($timePeriod);

            return RevenueSummary::whereBetween('transaction_date', [$start, $end])
                ->select(
                    'patient_id',
                    DB::raw('SUM(medication_revenue) as medications'),
                    DB::raw('SUM(appointment_revenue) as appointments'),
                    DB::raw('SUM(lab_revenue) as labs'),
                    DB::raw('SUM(admission_revenue) as admissions'),
                    DB::raw('(SUM(medication_revenue) + SUM(appointment_revenue) + SUM(lab_revenue) + SUM(admission_revenue)) as total_contribution')
                )
                ->groupBy('patient_id')
                ->orderByDesc('total_contribution')
                ->with('patient') // Assuming patient relationship exists
                ->paginate($perPage);
        });
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
            'med' => (float) ($data->med ?? 0),
            'appt' => (float) ($data->appt ?? 0),
            'lab' => (float) ($data->lab ?? 0),
            'adm' => (float) ($data->adm ?? 0),
        ];
    }

    private function getDateRange(string $period): array
    {
        $now = Carbon::now();

        return match ($period) {
            'today' => [$now->copy()->startOfDay(), $now->endOfDay()],
            'week' => [$now->copy()->startOfWeek(), $now->endOfWeek()],
            'year' => [$now->copy()->startOfYear(), $now->endOfYear()],
            default => [$now->copy()->startOfMonth(), $now->endOfMonth()],
        };
    }

    private function getPreviousDateRange(string $period): array
    {
        $now = Carbon::now();

        return match ($period) {
            'today' => [$now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay()],
            'week' => [$now->copy()->subWeek()->startOfWeek(), $now->copy()->subWeek()->endOfWeek()],
            'year' => [$now->copy()->subYear()->startOfYear(), $now->copy()->subYear()->endOfYear()],
            default => [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()],
        };
    }
}
