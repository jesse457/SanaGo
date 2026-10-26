<?php

namespace App\Services\Billing;

use App\Enums\AdmissionStatus;
use App\Enums\ServiceCategory;
use App\Models\Admission;
use App\Models\AdmissionService as PerformedAdmissionService;
use App\Models\BedType;
use App\Models\CashPoint;
use App\Models\InvoiceItem;
use App\Models\LabTestDefinition;
use App\Models\Medication;
use App\Models\Patient;
use App\Models\Service;

/**
 * What the cash desk may sell, resolved from the record that owns each module.
 *
 * The counter must never ask the cashier to name a price or remember which
 * drug exists: every pickable line is read from the record that module already
 * keeps, and its price from the service catalogue behind it.
 *
 *  - Drugs come from the pharmacy stock (`medications`), priced by the
 *    catalogue row linked to the drug, so the counter and the pharmacy never
 *    disagree on the price of a box.
 *  - Lab tests come from the laboratory catalogue (`lab_test_definitions`).
 *  - The bed charge of a stay comes from the bed type's catalogue row, and the
 *    stay itself is detected from the patient's open admission instead of being
 *    described by hand.
 *  - Consultations, procedures, nursing and imaging come from the price list.
 *
 * A row keeps the shape the counter sends back when the cashier pays:
 * `source` + `source_id` identify the record, `item_type` + `item_id` are what
 * the invoice line stores.
 */
class CounterCatalogue
{
    /** A price-list row (`services`). */
    public const SOURCE_SERVICE = 'service';

    /** A pharmacy stock row (`medications`). */
    public const SOURCE_MEDICATION = 'medication';

    /** A laboratory catalogue row (`lab_test_definitions`). */
    public const SOURCE_LAB_TEST = 'lab_test';

    /** The bed/day charge of the patient's open admission. */
    public const SOURCE_ADMISSION_BED = 'admission_bed';

    /** A service already performed during the patient's stay. */
    public const SOURCE_ADMISSION_SERVICE = 'admission_service';

    /**
     * Categories billed through their own record, never through the flat price
     * list: a drug is billed from the stock, a test from the laboratory
     * catalogue and a bed day from the stay that is running.
     *
     * @var array<int, ServiceCategory>
     */
    private const OWN_RECORD_CATEGORIES = [
        ServiceCategory::Medication,
        ServiceCategory::Lab,
        ServiceCategory::Bed,
    ];

    /**
     * Everything this till may sell, in the order the counter shows it.
     *
     * @return array<int, array<string, mixed>>
     */
    public function picks(?CashPoint $point = null): array
    {
        $rows = [
            ...$this->medicationRows($point),
            ...$this->labTestRows($point),
            ...$this->serviceRows($point),
        ];

        return $this->sorted($rows);
    }

    /**
     * The modules this till actually has something to sell in.
     *
     * @return array<int, array{value: string, label: string}>
     */
    public function modules(?CashPoint $point = null): array
    {
        $sellable = array_values(array_unique(array_column($this->picks($point), 'module')));

        return array_values(array_filter(
            ServiceCategory::modules(),
            fn (array $module): bool => in_array($module['value'], $sellable, true),
        ));
    }

    /**
     * The stay the patient is in right now, with what it has accrued so far.
     *
     * Detected, never typed: an admission that was discharged (or that carries
     * a discharge date) is over, so only an open stay comes back.
     *
     * @return array<string, mixed>|null
     */
    public function ongoingAdmission(Patient $patient): ?array
    {
        $admission = $this->openAdmission($patient);

        if ($admission === null) {
            return null;
        }

        $nights = $this->nightsOf($admission);

        return [
            'id' => $admission->id,
            'admitted_at' => $admission->admission_date?->format('Y-m-d'),
            'nights' => $nights,
            'ward' => $admission->bed?->ward?->name,
            'bed' => $admission->bed?->code,
            'bed_type' => $admission->bed?->bedType?->name,
            'doctor' => $admission->attendingDoctor?->name,
            'reason' => $admission->reason_for_admission,
            'suggestions' => $this->staySuggestions($admission, $nights),
        ];
    }

    /**
     * Resolve one pick to the invoice line it bills.
     *
     * The counter shows rows produced here and bills through the same method,
     * so what the cashier saw is exactly what the patient is charged. Rows that
     * belong to a patient (a bed day, a service performed during the stay) are
     * only resolvable for the patient they belong to.
     *
     * @return array<string, mixed>|null
     */
    public function resolve(string $source, int $id, ?Patient $patient = null): ?array
    {
        return match ($source) {
            self::SOURCE_MEDICATION => $this->resolveMedication($id),
            self::SOURCE_LAB_TEST => $this->resolveLabTest($id),
            self::SOURCE_ADMISSION_BED => $this->resolveAdmissionBed($id, $patient),
            self::SOURCE_ADMISSION_SERVICE => $this->resolveAdmissionService($id, $patient),
            default => $this->resolveService($id),
        };
    }

    /**
     * Whether a catalogue row may be billed by this till.
     */
    public function sellable(?CashPoint $point, array $row): bool
    {
        return $point === null || $point->canCollect((string) $row['item_type']);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function medicationRows(?CashPoint $point): array
    {
        if (! $this->tillSells($point, 'pharmacy')) {
            return [];
        }

        return Medication::query()
            ->where('is_active', true)
            ->with('service:id,name,code,default_price')
            ->orderBy('name')
            ->get(['id', 'name', 'strength', 'dosage_units', 'total_stock', 'unit_cost', 'service_id'])
            ->map(fn (Medication $medication): array => $this->medicationRow($medication))
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function labTestRows(?CashPoint $point): array
    {
        if (! $this->tillSells($point, 'lab_test')) {
            return [];
        }

        return LabTestDefinition::query()
            ->where('is_active', true)
            ->with('service:id,name,code,default_price')
            ->orderBy('test_name')
            ->get(['id', 'test_name', 'code', 'price', 'units', 'service_id'])
            ->map(fn (LabTestDefinition $definition): array => $this->labTestRow($definition))
            ->all();
    }

    /**
     * Price-list rows for everything the catalogue does not own another way.
     *
     * @return array<int, array<string, mixed>>
     */
    private function serviceRows(?CashPoint $point): array
    {
        $linked = Medication::query()
            ->whereNotNull('service_id')
            ->pluck('service_id')
            ->merge(LabTestDefinition::query()->whereNotNull('service_id')->pluck('service_id'))
            ->all();

        return Service::query()
            ->where('is_active', true)
            ->whereNotIn('category', array_map(
                fn (ServiceCategory $category): string => $category->value,
                self::OWN_RECORD_CATEGORIES,
            ))
            ->when($linked !== [], fn ($query) => $query->whereNotIn('id', $linked))
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'category', 'default_price', 'requires_upfront_payment'])
            ->map(fn (Service $service): array => $this->serviceRow($service))
            ->filter(fn (array $row): bool => $this->sellable($point, $row))
            ->values()
            ->all();
    }

    /**
     * What an open stay has already run up, ready to be put on the bill.
     *
     * @return array<int, array<string, mixed>>
     */
    private function staySuggestions(Admission $admission, int $nights): array
    {
        $suggestions = [];

        $bed = $this->resolveAdmissionBed($admission->id, $admission->patient);

        if ($bed !== null && $bed['price'] > 0) {
            $bed['quantity'] = $nights;
            $bed['note'] = "{$nights} × ".number_format((float) $bed['price'], 0, '.', ' ').' FCFA';
            $suggestions[] = $bed;
        }

        $billedServiceIds = InvoiceItem::query()
            ->where('item_type', 'admission_service')
            ->whereNotNull('item_id')
            ->pluck('item_id')
            ->all();

        $performed = $admission->admissionServices()
            ->with('service:id,name')
            ->orderBy('id')
            ->get()
            ->reject(fn (PerformedAdmissionService $service): bool => in_array($service->id, $billedServiceIds, true));

        foreach ($performed as $service) {
            $suggestions[] = [
                'key' => self::SOURCE_ADMISSION_SERVICE.':'.$service->id,
                'source' => self::SOURCE_ADMISSION_SERVICE,
                'source_id' => $service->id,
                'item_type' => 'admission_service',
                'item_id' => $service->id,
                'module' => 'admission',
                'name' => $service->service?->name ?? 'Service',
                'code' => null,
                'price' => (float) $service->unit_price,
                'unit' => null,
                'stock' => null,
                'quantity' => max(1, (int) $service->quantity),
                'note' => $service->notes,
            ];
        }

        return $suggestions;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function resolveMedication(int $id): ?array
    {
        $medication = Medication::query()
            ->where('is_active', true)
            ->with('service:id,name,code,default_price')
            ->find($id);

        return $medication === null ? null : $this->medicationRow($medication);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function resolveLabTest(int $id): ?array
    {
        $definition = LabTestDefinition::query()
            ->where('is_active', true)
            ->with('service:id,name,code,default_price')
            ->find($id);

        return $definition === null ? null : $this->labTestRow($definition);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function resolveService(int $id): ?array
    {
        $service = Service::query()->where('is_active', true)->find($id);

        return $service === null ? null : $this->serviceRow($service);
    }

    /**
     * The bed/day charge of one stay, priced by the catalogue row of its bed.
     *
     * @return array<string, mixed>|null
     */
    private function resolveAdmissionBed(int $id, ?Patient $patient): ?array
    {
        if ($patient === null) {
            return null;
        }

        $admission = $this->openAdmission($patient, $id);

        if ($admission === null) {
            return null;
        }

        return $this->admissionBedRow($admission);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function resolveAdmissionService(int $id, ?Patient $patient): ?array
    {
        if ($patient === null) {
            return null;
        }

        $service = PerformedAdmissionService::query()
            ->whereHas('admission', fn ($query) => $query->where('patient_id', $patient->id))
            ->with('service:id,name')
            ->find($id);

        if ($service === null) {
            return null;
        }

        return [
            'key' => self::SOURCE_ADMISSION_SERVICE.':'.$service->id,
            'source' => self::SOURCE_ADMISSION_SERVICE,
            'source_id' => $service->id,
            'item_type' => 'admission_service',
            'item_id' => $service->id,
            'module' => 'admission',
            'name' => $service->service?->name ?? 'Service',
            'code' => null,
            'price' => (float) $service->unit_price,
            'unit' => null,
            'stock' => null,
            'quantity' => max(1, (int) $service->quantity),
            'description' => $service->service?->name ?? 'Service',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function admissionBedRow(Admission $admission): array
    {
        $bedType = $admission->bed?->bedType;
        $service = $this->bedService($bedType);
        $price = $service !== null
            ? (float) $service->default_price
            : (float) ($bedType?->daily_rate ?? 0);
        $name = $service?->name
            ?? ($bedType?->name !== null ? "Lit/jour — {$bedType->name}" : 'Hospitalisation');

        return [
            'key' => self::SOURCE_ADMISSION_BED.':'.$admission->id,
            'source' => self::SOURCE_ADMISSION_BED,
            'source_id' => $admission->id,
            'item_type' => 'bed',
            // The line belongs to the stay, so the discharge bill can count
            // what the counter already took for it.
            'item_id' => $admission->id,
            'module' => 'admission',
            'name' => $name,
            'code' => $service?->code,
            'price' => $price,
            'unit' => 'nuit',
            'stock' => null,
            'quantity' => $this->nightsOf($admission),
            'description' => $name,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function medicationRow(Medication $medication): array
    {
        $name = trim($medication->name.' '.($medication->strength ?? ''));

        return [
            'key' => self::SOURCE_MEDICATION.':'.$medication->id,
            'source' => self::SOURCE_MEDICATION,
            'source_id' => $medication->id,
            'item_type' => 'pharmacy',
            'item_id' => $medication->id,
            'module' => 'pharmacy',
            'name' => $name,
            'code' => $medication->service?->code,
            // The pharmacy price lives on the catalogue row of the drug, so the
            // counter and the pharmacy quote the same figure.
            'price' => $medication->service !== null
                ? (float) $medication->service->default_price
                : (float) $medication->unit_cost,
            'unit' => $medication->dosage_units,
            'stock' => (int) $medication->total_stock,
            'quantity' => 1,
            'description' => $name,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function labTestRow(LabTestDefinition $definition): array
    {
        return [
            'key' => self::SOURCE_LAB_TEST.':'.$definition->id,
            'source' => self::SOURCE_LAB_TEST,
            'source_id' => $definition->id,
            'item_type' => 'lab_test',
            'item_id' => $definition->id,
            'module' => 'laboratory',
            'name' => $definition->test_name,
            'code' => $definition->code,
            'price' => $definition->service !== null
                ? (float) $definition->service->default_price
                : (float) $definition->price,
            'unit' => $definition->units,
            'stock' => null,
            'quantity' => 1,
            'description' => 'Lab Test: '.$definition->test_name,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function serviceRow(Service $service): array
    {
        return [
            'key' => self::SOURCE_SERVICE.':'.$service->id,
            'source' => self::SOURCE_SERVICE,
            'source_id' => $service->id,
            'item_type' => $service->category?->value ?? 'service',
            'item_id' => $service->id,
            'module' => $service->category?->module() ?? 'other',
            'name' => $service->name,
            'code' => $service->code,
            'price' => (float) $service->default_price,
            'unit' => null,
            'stock' => null,
            'quantity' => 1,
            'description' => $service->name,
        ];
    }

    /**
     * The patient's open stay: admitted, not discharged, and (optionally) a
     * named one.
     */
    private function openAdmission(Patient $patient, ?int $admissionId = null): ?Admission
    {
        return Admission::query()
            ->where('patient_id', $patient->id)
            ->where('status', AdmissionStatus::Admitted)
            ->whereNull('discharge_date')
            ->when($admissionId !== null, fn ($query) => $query->whereKey($admissionId))
            ->with([
                'bed:id,code,ward_id,bed_type_id',
                'bed.ward:id,name',
                'bed.bedType:id,name,daily_rate,service_id',
                'bed.bedType.service:id,name,code,default_price',
                'attendingDoctor:id,name',
            ])
            ->latest('admission_date')
            ->first();
    }

    /**
     * Nights run up so far, counting the one the patient is spending now.
     */
    private function nightsOf(Admission $admission): int
    {
        $admittedAt = $admission->admission_date?->copy()->startOfDay() ?? today();

        return max(1, (int) $admittedAt->diffInDays(today()));
    }

    /**
     * The catalogue row that prices a bed type.
     */
    private function bedService(?BedType $bedType): ?Service
    {
        if ($bedType === null) {
            return null;
        }

        if ($bedType->service !== null && $bedType->service->default_price !== null) {
            return $bedType->service;
        }

        return Service::query()
            ->where('source_model', BedType::class)
            ->where('source_id', $bedType->id)
            ->where('is_active', true)
            ->first();
    }

    private function tillSells(?CashPoint $point, string $itemType): bool
    {
        return $point === null || $point->canCollect($itemType);
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<int, array<string, mixed>>
     */
    private function sorted(array $rows): array
    {
        $order = array_keys(ServiceCategory::MODULES);

        usort($rows, function (array $left, array $right) use ($order): int {
            $leftRank = array_search($left['module'], $order, true);
            $rightRank = array_search($right['module'], $order, true);

            return [$leftRank === false ? PHP_INT_MAX : $leftRank, $left['name']]
                <=> [$rightRank === false ? PHP_INT_MAX : $rightRank, $right['name']];
        });

        return array_values($rows);
    }
}
