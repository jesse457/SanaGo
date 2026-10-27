<?php

declare(strict_types=1);

use App\Enums\PlanFeature;
use App\Models\Plan;
use App\Models\Tenant;
use App\Services\Platform\PlanLimitService;
use App\Services\Platform\SubscriptionService;
use App\Support\PlatformContext;
use Database\Seeders\SubscriptionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Tests\Helpers\migrateTenantTables;

uses(RefreshDatabase::class);

/**
 * The capability keys a hospital licence is supposed to grant: everything the
 * platform can do, minus API access and SMS credit.
 *
 * @return array<int, string>
 */
function hospitalPlanKeys(): array
{
    return array_values(array_diff(
        PlanFeature::values(),
        [PlanFeature::ApiAccess->value, PlanFeature::SmsNotifications->value],
    ));
}

describe('the hospital plan feature map', function () {
    it('grants every capability except API access and SMS', function () {
        $flags = PlanFeature::forPlanSlug('hospital');

        expect(array_keys($flags))->toEqualCanonicalizing(hospitalPlanKeys())
            ->and(array_filter($flags))->toHaveCount(count(hospitalPlanKeys()))
            ->and($flags)->not->toHaveKey(PlanFeature::ApiAccess->value)
            ->and($flags)->not->toHaveKey(PlanFeature::SmsNotifications->value);
    });

    it('keeps the full clinical, diagnostics and money floor on', function () {
        $flags = PlanFeature::forPlanSlug('hospital');

        foreach ([
            PlanFeature::Patients,
            PlanFeature::Appointments,
            PlanFeature::Consultations,
            PlanFeature::Admissions,
            PlanFeature::Vitals,
            PlanFeature::Laboratory,
            PlanFeature::Radiology,
            PlanFeature::Pharmacy,
            PlanFeature::Inventory,
            PlanFeature::Surgery,
            PlanFeature::Emergency,
            PlanFeature::Billing,
            PlanFeature::CashRegister,
            PlanFeature::InsuranceClaims,
            PlanFeature::Certificates,
            PlanFeature::Reports,
            PlanFeature::AuditLogs,
            PlanFeature::CustomBranding,
        ] as $feature) {
            expect($flags[$feature->value] ?? false)->toBeTrue();
        }
    });

    it('sits one step below enterprise, differing only by API access and SMS', function () {
        $hospital = PlanFeature::forPlanSlug('hospital');
        $enterprise = PlanFeature::forPlanSlug('enterprise');

        expect(array_values(array_diff_key($enterprise, $hospital)))
            ->toEqualCanonicalizing([PlanFeature::ApiAccess->value, PlanFeature::SmsNotifications->value])
            ->and(array_diff_key($hospital, $enterprise))->toBe([]);
    });

    it('resolves its flags from the slug when a plan carries no stored map', function () {
        $plan = Plan::factory()->create([
            'slug' => 'hospital',
            'name' => 'Hospital',
            'feature_flags_json' => null,
        ]);

        expect($plan->featureFlags())->toEqual(PlanFeature::forPlanSlug('hospital'));
    });
});

describe('the seeded hospital plan', function () {
    it('is published in the plan catalogue with its capability map', function () {
        $this->seed(SubscriptionSeeder::class);

        $plan = Plan::query()->where('slug', 'hospital')->first();

        expect($plan)->not->toBeNull()
            ->and($plan->is_active)->toBeTrue()
            ->and($plan->monthly_price)->toBe('75000.00')
            ->and($plan->featureFlags())->toEqualCanonicalizing(PlanFeature::forPlanSlug('hospital'))
            ->and($plan->featureFlags()[PlanFeature::Radiology->value] ?? false)->toBeTrue()
            ->and($plan->featureFlags()[PlanFeature::ApiAccess->value] ?? false)->toBeFalse()
            ->and($plan->featureFlags()[PlanFeature::SmsNotifications->value] ?? false)->toBeFalse();
    });
});

describe('a tenant on the hospital plan', function () {
    beforeEach(function () {
        migrateTenantTables();

        $this->tenant = Tenant::factory()->create([
            'id' => 'hopital-complet',
            'name' => 'Hôpital Complet',
        ]);

        $this->plan = Plan::factory()->hospital()->create();

        app(SubscriptionService::class)->assignPlan($this->tenant, $this->plan);
    });

    it('opens the whole app except the two excluded capabilities', function () {
        actAsTenant($this->tenant);

        $limits = app(PlanLimitService::class);

        expect($limits->featureEnabled(PlanFeature::Radiology))->toBeTrue()
            ->and($limits->featureEnabled(PlanFeature::Surgery))->toBeTrue()
            ->and($limits->featureEnabled(PlanFeature::Certificates))->toBeTrue()
            ->and($limits->featureEnabled(PlanFeature::CustomBranding))->toBeTrue()
            ->and($limits->featureEnabled(PlanFeature::ApiAccess))->toBeFalse()
            ->and($limits->featureEnabled(PlanFeature::SmsNotifications))->toBeFalse();
    });

    it('exposes exactly the excluded capabilities as unavailable in the plan context', function () {
        actAsTenant($this->tenant);

        $features = PlatformContext::features($this->tenant->fresh());

        expect(array_keys(array_filter($features)))->toEqualCanonicalizing(hospitalPlanKeys())
            ->and($features)->not->toHaveKey(PlanFeature::ApiAccess->value)
            ->and($features)->not->toHaveKey(PlanFeature::SmsNotifications->value);
    });
});
