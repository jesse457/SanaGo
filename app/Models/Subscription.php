<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Subscription extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'plan',
        'status',
        'amount',
        'currency',
        'billing_cycle',
        'starts_at',
        'ends_at',
        'trial_ends_at',
        'cancelled_at',
        'features',
        'max_users',
        'max_storage',
        'stripe_subscription_id',
        'stripe_customer_id',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'features' => 'array',
        'metadata' => 'array',
    ];

    // Constants for subscription plans
    const PLAN_BASIC = 'basic';

    const PLAN_STANDARD = 'standard';

    const PLAN_ENTERPRISE = 'enterprise';

    // Constants for subscription status
    const STATUS_ACTIVE = 'active';

    const STATUS_INACTIVE = 'inactive';

    const STATUS_CANCELLED = 'cancelled';

    const STATUS_EXPIRED = 'expired';

    const STATUS_SUSPENDED = 'suspended';

    // Constants for billing cycles
    const BILLING_MONTHLY = 'monthly';

    const BILLING_YEARLY = 'yearly';

    /**
     * Boot method to set amount based on plan
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subscription) {
            if (empty($subscription->amount) && ! empty($subscription->plan)) {
                $subscription->amount = $subscription->getPlanAmount();
            }
        });

        static::updating(function ($subscription) {
            if ($subscription->isDirty('plan') && empty($subscription->amount)) {
                $subscription->amount = $subscription->getPlanAmount();
            }
        });
    }

    /**
     * Get the amount for the current plan
     */
    public function getPlanAmount()
    {
        return match ($this->plan) {
            self::PLAN_BASIC => 15000,
            self::PLAN_STANDARD => 30000,
            self::PLAN_ENTERPRISE => 100000,
            default => 0,
        };
    }

    /**
     * Check if subscription is active
     */
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE &&
               (! $this->ends_at || $this->ends_at->isFuture());
    }

    /**
     * Check if subscription is on trial
     */
    public function onTrial()
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Check if subscription is cancelled
     */
    public function isCancelled()
    {
        return $this->status === self::STATUS_CANCELLED ||
               $this->cancelled_at !== null;
    }

    /**
     * Check if subscription has expired
     */
    public function isExpired()
    {
        return $this->ends_at && $this->ends_at->isPast();
    }

    /**
     * Get the plan features
     */
    public function getPlanFeatures()
    {
        return $this->features ?? $this->getDefaultFeatures();
    }

    /**
     * Get default features for each plan
     */
    public function getDefaultFeatures()
    {
        return match ($this->plan) {
            self::PLAN_BASIC => [
                'max_users' => 10,
                'max_storage' => 1024, // 1GB
                'api_access' => false,
                'priority_support' => false,
                'custom_domain' => false,
                'advanced_analytics' => false,
            ],
            self::PLAN_STANDARD => [
                'max_users' => 50,
                'max_storage' => 5120, // 5GB
                'api_access' => true,
                'priority_support' => false,
                'custom_domain' => true,
                'advanced_analytics' => false,
            ],

            self::PLAN_ENTERPRISE => [
                'max_users' => -1, // Unlimited
                'max_storage' => -1, // Unlimited
                'api_access' => true,
                'priority_support' => true,
                'custom_domain' => true,
                'advanced_analytics' => true,
                'dedicated_support' => true,
                'custom_integrations' => true,
            ],
            default => [],
        };
    }

    /**
     * Check if feature is available for this plan
     */
    public function hasFeature($feature)
    {
        $features = $this->getPlanFeatures();

        return isset($features[$feature]) && $features[$feature];
    }

    /**
     * Get the next billing date
     */
    public function getNextBillingDate()
    {
        if (! $this->isActive()) {
            return null;
        }

        $billingCycle = $this->billing_cycle;
        $startDate = $this->starts_at;

        return match ($billingCycle) {
            self::BILLING_MONTHLY => $startDate->copy()->addMonth(),
            self::BILLING_YEARLY => $startDate->copy()->addYear(),
            default => null,
        };
    }

    /**
     * Get the display name for the plan
     */
    public function getPlanDisplayName()
    {
        return match ($this->plan) {
            self::PLAN_BASIC => 'Basic',
            self::PLAN_STANDARD => 'Standard',

            self::PLAN_ENTERPRISE => 'Enterprise',
            default => ucfirst($this->plan),
        };
    }

    /**
     * Get the display name for the status
     */
    public function getStatusDisplayName()
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_EXPIRED => 'Expired',
            self::STATUS_SUSPENDED => 'Suspended',
            default => ucfirst($this->status),
        };
    }

    /**
     * Scope a query to only include active subscriptions.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->where(function ($q) {
                $q->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            });
    }

    /**
     * Scope a query to only include subscriptions by plan.
     */
    public function scopeByPlan($query, $plan)
    {
        return $query->where('plan', $plan);
    }

    /**
     * Scope a query to only include expiring subscriptions.
     */
    public function scopeExpiringSoon($query, $days = 7)
    {
        return $query->where('ends_at', '<=', now()->addDays($days))
            ->where('ends_at', '>', now())
            ->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Cancel the subscription
     */
    public function cancel($immediately = false)
    {
        if ($immediately) {
            $this->update([
                'status' => self::STATUS_CANCELLED,
                'cancelled_at' => now(),
                'ends_at' => now(),
            ]);
        } else {
            $this->update([
                'status' => self::STATUS_CANCELLED,
                'cancelled_at' => now(),
            ]);
        }

        return $this;
    }

    /**
     * Resume a cancelled subscription
     */
    public function resume()
    {
        if ($this->isCancelled() && $this->ends_at && $this->ends_at->isFuture()) {
            $this->update([
                'status' => self::STATUS_ACTIVE,
                'cancelled_at' => null,
            ]);
        }

        return $this;
    }

    /**
     * ==========================================
     * STORAGE CALCULATION METHODS
     * ==========================================
     */

    /**
     * Get total usage (DB + S3) in bytes.
     * Note: This operation can be heavy. consider caching the result.
     */
    public function getUsedStorageInBytes(): int
    {
        return $this->getDatabaseUsageBytes() + $this->getS3UsageBytes();
    }

    /**
     * Calculate usage percentage against the plan limit.
     */
    public function getStorageUsagePercentage(): float
    {
        // -1 indicates unlimited storage in your logic
        $limit = $this->getPlanFeatures()['max_storage'] ?? 0;

        if ($limit == -1) {
            return 0;
        }

        // Convert limit from MB (as stored in features) to Bytes for comparison
        $limitBytes = $limit * 1024 * 1024;

        if ($limitBytes <= 0) {
            return 100;
        }

        $usedBytes = $this->getUsedStorageInBytes();

        return round(($usedBytes / $limitBytes) * 100, 2);
    }

    /**
     * Calculate the size of files in S3 for this tenant.
     * Assumes Stancl structure: 'tenants/{tenant_id}/'
     */
    public function getS3UsageBytes(): int
    {
        // 1. Determine the path.
        // Stancl Tenancy usually stores files in directories named after the tenant key.
        $tenantKey = $this->tenant_id;
        $directory = "tenants/{$tenantKey}/"; // Adjust based on your filesystem.php config

        // 2. Check if S3 is configured
        if (config('filesystems.default') !== 's3' && config('filesystems.disks.s3') === null) {
            return 0;
        }

        $size = 0;

        // 3. Recursive lookup (Heavy operation - recommend running in a Job)
        // Note: We use 's3' disk explicitly here.
        $files = Storage::disk('s3')->allFiles($directory);

        foreach ($files as $file) {
            $size += Storage::disk('s3')->size($file);
        }

        return $size;
    }

    /**
     * Calculate size of Database rows for this tenant.
     * Uses PostgreSQL specific function: pg_column_size
     */
    public function getDatabaseUsageBytes(): int
    {
        $tenantId = $this->tenant_id;
        $totalSize = 0;

        // List the tables that store tenant data.
        // Scanning ALL tables in a large DB is slow. List the heavy ones here.
        $tablesToCheck = [
            'users',
            'activity_logs',
            'files',
            // 'patients', // Add your specific hospital app tables here
            // 'appointments',
        ];

        foreach ($tablesToCheck as $table) {
            // Verify table exists to prevent SQL errors
            if (! \Illuminate\Support\Facades\Schema::hasTable($table)) {
                continue;
            }

            // PostgreSQL query to sum the size of rows belonging to this tenant
            $size = DB::table($table)
                ->where('tenant_id', $tenantId)
                ->sum(DB::raw('pg_column_size(*)')); // pg_column_size includes data + overhead

            $totalSize += $size;
        }

        return $totalSize;
    }

    /**
     * Helper to format bytes to human readable string (MB, GB)
     */
    public function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision).' '.$units[$pow];
    }
}
