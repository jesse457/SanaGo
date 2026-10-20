<?php

namespace App\Models;

use App\Enums\PlanFeature;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $connection = 'central';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'monthly_price',
        'yearly_price',
        'currency',
        'is_active',
        'is_featured',
        'sort_order',
        'max_users_per_tenant',
        'max_patients_per_tenant',
        'storage_gb',
        'trial_days',
        'grace_days',
        'api_rate_limit',
        'features_json',
        'feature_flags_json',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
        'sort_order' => 'integer',
        'max_users_per_tenant' => 'integer',
        'max_patients_per_tenant' => 'integer',
        'storage_gb' => 'integer',
        'trial_days' => 'integer',
        'grace_days' => 'integer',
        'api_rate_limit' => 'integer',
        'features_json' => 'array',
        'feature_flags_json' => 'array',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Price for a billing cycle. Annual plans fall back to a 12x monthly rate
     * when only a monthly price is configured.
     */
    public function priceFor(string $cycle): float
    {
        $cycle = $cycle === 'yearly' ? 'yearly' : 'monthly';

        if ($cycle === 'yearly') {
            return (float) ($this->yearly_price ?? ((float) $this->monthly_price * 12));
        }

        return (float) ($this->monthly_price ?? ((float) $this->yearly_price / 12));
    }

    /**
     * Capability flags granted by this plan keyed by feature.
     *
     * @return array<string, bool>
     */
    public function featureFlags(): array
    {
        $flags = $this->feature_flags_json ?? [];

        if ($flags === []) {
            $flags = PlanFeature::forPlanSlug($this->slug);
        }

        return array_map(fn ($enabled) => (bool) $enabled, $flags);
    }

    /**
     * Quota limits granted by this plan.
     *
     * @return array{users: int|null, patients: int|null, storage_gb: int|null}
     */
    public function limits(): array
    {
        return [
            'users' => $this->max_users_per_tenant,
            'patients' => $this->max_patients_per_tenant,
            'storage_gb' => $this->storage_gb,
        ];
    }

    public function trialDays(): int
    {
        return (int) ($this->trial_days ?? 14);
    }

    public function graceDays(): int
    {
        return (int) ($this->grace_days ?? 3);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
