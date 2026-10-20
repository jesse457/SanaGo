<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Factory<Tenant>
 */
class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    /**
     * Tenant creation normally triggers database creation + migration + demo
     * seeding (stancl/tenancy pipeline). Tests only need the record itself, so
     * the events are muted unless `withDatabase()` is used.
     */
    protected bool $provisionDatabase = false;

    public function definition(): array
    {
        $slug = fake()->unique()->slug(2);

        return [
            'id' => $slug,
            'name' => fake()->company(),
            'is_active' => true,
            'suspended_at' => null,
            'trial_ends_at' => null,
        ];
    }

    public function suspended(): static
    {
        return $this->state(fn () => [
            'is_active' => false,
            'suspended_at' => now(),
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn () => [
            'archived_at' => now(),
            'archived_reason' => 'Test archive',
        ]);
    }

    /**
     * Run the real provisioning pipeline (creates + migrates the tenant DB).
     */
    public function withDatabase(): static
    {
        $this->provisionDatabase = true;

        return $this;
    }

    public function create($attributes = [], ?Model $parent = null)
    {
        if ($this->provisionDatabase) {
            return parent::create($attributes, $parent);
        }

        return Tenant::withoutEvents(fn () => parent::create($attributes, $parent));
    }
}
