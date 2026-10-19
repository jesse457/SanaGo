<?php

namespace App\Console\Commands;

use App\Enums\ServiceCategory;
use App\Models\BedType;
use App\Models\LabTestDefinition;
use App\Models\Medication;
use App\Models\Service;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('app:sync-services-from-entities')]
#[Description('Link existing lab tests, bed types, and medications to the services catalog. Prices stay in sync with the source entity.')]
class SyncServicesFromEntities extends Command
{
    public function handle(): int
    {
        $this->info('Syncing existing entities to services catalog...');

        $linked = 0;

        DB::transaction(function () use (&$linked) {
            $linked += $this->syncLabTests();
            $linked += $this->syncBedTypes();
            $linked += $this->syncMedications();
        });

        $this->info("Done. {$linked} entities linked.");

        return self::SUCCESS;
    }

    private function syncLabTests(): int
    {
        $count = 0;
        $tests = LabTestDefinition::whereNull('service_id')->get();

        foreach ($tests as $test) {
            // Check if a service already exists for this lab test via source
            $service = Service::where('source_model', LabTestDefinition::class)
                ->where('source_id', $test->id)
                ->first();

            if (! $service) {
                $service = Service::create([
                    'name' => $test->test_name,
                    'code' => $test->code ?? 'LAB-'.strtoupper(substr((string) $test->id, 0, 4)),
                    'category' => ServiceCategory::Lab,
                    'description' => $test->description,
                    'source_model' => LabTestDefinition::class,
                    'source_id' => $test->id,
                    'default_price' => $test->price ?? 0,
                    'is_active' => $test->is_active,
                    'requires_upfront_payment' => true,
                ]);
            } else {
                // Sync price from source
                $service->update(['default_price' => $test->price ?? 0]);
            }

            $test->update(['service_id' => $service->id]);
            $count++;
        }

        $this->line("  ✓ {$count} lab test(s) linked");

        return $count;
    }

    private function syncBedTypes(): int
    {
        $count = 0;
        $beds = BedType::whereNull('service_id')->get();

        foreach ($beds as $bed) {
            $service = Service::where('source_model', BedType::class)
                ->where('source_id', $bed->id)
                ->first();

            if (! $service) {
                $service = Service::create([
                    'name' => $bed->name,
                    'code' => 'BED-'.strtoupper(substr((string) $bed->id, 0, 4)),
                    'category' => ServiceCategory::Bed,
                    'description' => $bed->description,
                    'source_model' => BedType::class,
                    'source_id' => $bed->id,
                    'default_price' => $bed->daily_rate ?? 0,
                    'is_active' => true,
                    'requires_upfront_payment' => false,
                ]);
            } else {
                $service->update(['default_price' => $bed->daily_rate ?? 0]);
            }

            $bed->update(['service_id' => $service->id]);
            $count++;
        }

        $this->line("  ✓ {$count} bed type(s) linked");

        return $count;
    }

    private function syncMedications(): int
    {
        $count = 0;
        $meds = Medication::whereNull('service_id')->get();

        foreach ($meds as $med) {
            $service = Service::where('source_model', Medication::class)
                ->where('source_id', $med->id)
                ->first();

            $name = $med->strength
                ? "{$med->name} {$med->strength}"
                : $med->name;

            if (! $service) {
                $code = 'MED-'.strtoupper(substr((string) $med->id, 0, 4));
                $service = Service::create([
                    'name' => $name,
                    'code' => $code,
                    'category' => ServiceCategory::Medication,
                    'description' => $med->description,
                    'source_model' => Medication::class,
                    'source_id' => $med->id,
                    'default_price' => $med->unit_cost ?? 0,
                    'is_active' => $med->is_active,
                    'requires_upfront_payment' => true,
                ]);
            } else {
                $service->update(['default_price' => $med->unit_cost ?? 0]);
            }

            $med->update(['service_id' => $service->id]);
            $count++;
        }

        $this->line("  ✓ {$count} medication(s) linked");

        return $count;
    }
}
