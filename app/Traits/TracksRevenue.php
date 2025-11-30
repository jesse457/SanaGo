<?php

namespace App\Traits;

use App\Models\RevenueSummary;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

trait TracksRevenue
{
    protected static function bootTracksRevenue(): void
    {
        static::created(function (Model $model) {
            $model->processRevenueChange('created');
        });

        static::updated(function (Model $model) {
            $model->processRevenueChange('updated');
        });

        static::deleted(function (Model $model) {
            $model->processRevenueChange('deleted');
        });
    }

    protected function processRevenueChange(string $event): void
    {
        try {
            $patientId = $this->getPatientId();
            if (!$patientId) {
                return;
            }

            // 1. Identify columns
            $columns = $this->getRevenueColumns();
            if (empty($columns)) {
                return;
            }

            // 2. Calculate NEW amounts
            $newDate = $this->getTransactionDate();
            $newAmounts = ($event === 'deleted') ? [] : $this->calculateRevenueAmounts(false);

            // 3. Calculate OLD amounts (for updates/deletes)
            $oldDate = null;
            $oldAmounts = [];

            if ($event === 'updated' || $event === 'deleted') {
                $oldDate = $this->getOriginalTransactionDate();
                $oldAmounts = $this->calculateRevenueAmounts(true);
            }

            // 4. DB Update: Subtract Old
            if (($event === 'updated' || $event === 'deleted') && $oldDate) {
                $this->updateSummaryRecord($patientId, $oldDate, $columns, $oldAmounts, 'subtract');
            }

            // 5. DB Update: Add New
            if (($event === 'created' || $event === 'updated') && $newDate) {
                $this->updateSummaryRecord($patientId, $newDate, $columns, $newAmounts, 'add');
            }

        } catch (Exception $e) {
            Log::error('TracksRevenue Error: ' . $e->getMessage(), [
                'model' => class_basename($this),
                'id' => $this->getKey()
            ]);
        }
    }

    /**
     * @param int|string $patientId
     * @param string $date
     * @param array<int, string> $columns
     * @param array<int, float> $amounts
     * @param string $operation
     */
    protected function updateSummaryRecord($patientId, string $date, array $columns, array $amounts, string $operation): void
    {
        if (array_sum($amounts) == 0) {
            return;
        }

        $summary = RevenueSummary::firstOrCreate(
            ['patient_id' => $patientId, 'transaction_date' => $date],
            [
                'medication_revenue' => 0,
                'appointment_revenue' => 0,
                'lab_revenue' => 0,
                'admission_revenue' => 0,
                'bed_fee_revenue' => 0
            ]
        );

        foreach ($columns as $index => $colName) {
            $amount = $amounts[$index] ?? 0.0;

            if ($amount > 0) {
                if ($operation === 'add') {
                    $summary->increment($colName, $amount);
                } else {
                    $summary->decrement($colName, $amount);
                }
            }
        }
    }

    /**
     * @return array<int, string>
     */
    protected function getRevenueColumns(): array
    {
        return match (class_basename($this)) {
            'Appointment' => ['appointment_revenue'],
            'Dispensation' => ['medication_revenue'],
            'LabResult' => ['lab_revenue'],
            'Admission' => ['admission_revenue', 'bed_fee_revenue'],
            default => []
        };
    }

    /**
     * @param bool $useOriginal
     * @return array<int, float>
     */
    protected function calculateRevenueAmounts(bool $useOriginal = false): array
    {
        $class = class_basename($this);
        $val = fn(string $field) => $useOriginal ? $this->getOriginal($field) : $this->getAttribute($field);

        if ($class === 'Appointment') {
            $status = (string) $val('status');
            $price = (float) ($val('price') ?? 0);
            return [$status === 'Completed' ? $price : 0.0];
        }

        if ($class === 'LabResult') {
            $status = (string) $val('status');
            $price = (float) ($val('price') ?? 0);
            return [$status === 'Completed' ? $price : 0.0];
        }

        if ($class === 'Dispensation') {
            return [(float) ($val('total_price') ?? 0)];
        }

        if ($class === 'Admission') {
            $status = (string) $val('status');

            // Only track revenue if Admitted or Discharged
            if (!in_array($status, ['Admitted', 'Discharged'])) {
                return [0.0, 0.0];
            }

            $startDateRaw = $val('admission_date');
            $dischargeDateRaw = $val('discharge_date');

            if (!$startDateRaw) {
                return [0.0, 0.0];
            }

            $start = Carbon::parse($startDateRaw);

            // LOGIC FIX:
            // If discharge_date is NULL (user hasn't discharged yet),
            // we assume it is DAY 1 (The initial admission).
            // We do NOT multiply by days elapsed since admission, because the bill isn't final.
            if (is_null($dischargeDateRaw)) {
                $days = 1;
            } else {
                // If discharged, we calculate the full duration inclusive.
                $end = Carbon::parse($dischargeDateRaw);
                $days = (int) $start->diffInDays($end) + 1;
            }

            // Ensure we never have negative or zero days for a valid calculation
            if ($days < 1) {
                $days = 1;
            }

            // 1. Observation Fee Calculation
            $obsFee = (float) ($val('observation_fee') ?? 0);
            $totalObs = (float) ($days * $obsFee);

            // 2. Bed Fee Calculation
            $bedPricePerDay = 0.0;
            // Eager load protection for Bed Type Price
            if ($this->bed && $this->bed->bedType) {
                $bedPricePerDay = (float) $this->bed->bedType->price_per_day;
            }

            $totalBed = (float) ($days * $bedPricePerDay);

            return [$totalObs, $totalBed];
        }

        return [];
    }

    /**
     * @return int|string|null
     */
    protected function getPatientId(): int|string|null
    {
        if ($this->getAttribute('patient_id')) {
            return $this->getAttribute('patient_id');
        }

        $class = class_basename($this);

        if ($class === 'Dispensation') {
            return $this->prescriptionItem?->prescription?->patient_id ?? null;
        }
        if ($class === 'LabResult') {
            return $this->consultation?->patient_id ?? null;
        }

        return null;
    }

    protected function getTransactionDate(): string
    {
        return $this->resolveDate(
            $this->getAttribute('admission_date') ??
            $this->getAttribute('appointment_date') ??
            $this->getAttribute('dispensed_at') ??
            $this->getAttribute('request_date') ??
            $this->getAttribute('created_at')
        );
    }

    protected function getOriginalTransactionDate(): string
    {
        return $this->resolveDate(
            $this->getOriginal('admission_date') ??
            $this->getOriginal('appointment_date') ??
            $this->getOriginal('dispensed_at') ??
            $this->getOriginal('request_date') ??
            $this->getOriginal('created_at')
        );
    }

    private function resolveDate(mixed $source): string
    {
        if (!$source) {
            return Carbon::today()->toDateString();
        }
        return Carbon::parse($source)->toDateString();
    }
}
