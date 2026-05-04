<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // Added Schema facade
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

    public function getPlanAmount()
    {
        return match ($this->plan) {
            self::PLAN_BASIC => 15000,
            self::PLAN_STANDARD => 30000,
            self::PLAN_ENTERPRISE => 100000,
            default => 0,
        };
    }

    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE &&
               (! $this->ends_at || $this->ends_at->isFuture());
    }

    public function onTrial()
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function isCancelled()
    {
        return $this->status === self::STATUS_CANCELLED ||
               $this->cancelled_at !== null;
    }

    public function isExpired()
    {
        return $this->ends_at && $this->ends_at->isPast();
    }

    public function getPlanFeatures()
    {
        return $this->features ?? $this->getDefaultFeatures();
    }

    public function getDefaultFeatures()
    {
        return match ($this->plan) {
            self::PLAN_BASIC => [
                'max_users' => 10,
                'max_storage' => 1024,
                'api_access' => false,
                'priority_support' => false,
                'custom_domain' => false,
                'advanced_analytics' => false,
            ],
            self::PLAN_STANDARD => [
                'max_users' => 50,
                'max_storage' => 5120,
                'api_access' => true,
                'priority_support' => false,
                'custom_domain' => true,
                'advanced_analytics' => false,
            ],
            self::PLAN_ENTERPRISE => [
                'max_users' => -1,
                'max_storage' => -1,
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

    public function hasFeature($feature)
    {
        $features = $this->getPlanFeatures();

        return isset($features[$feature]) && $features[$feature];
    }

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

    public function getPlanDisplayName()
    {
        return match ($this->plan) {
            self::PLAN_BASIC => 'Basic',
            self::PLAN_STANDARD => 'Standard',
            self::PLAN_ENTERPRISE => 'Enterprise',
            default => ucfirst($this->plan),
        };
    }

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

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->where(function ($q) {
                $q->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            });
    }

    public function scopeByPlan($query, $plan)
    {
        return $query->where('plan', $plan);
    }

    public function scopeExpiringSoon($query, $days = 7)
    {
        return $query->where('ends_at', '<=', now()->addDays($days))
            ->where('ends_at', '>', now())
            ->where('status', self::STATUS_ACTIVE);
    }

    public function cancel($immediately = false)
    {
        $data = [
            'status' => self::STATUS_CANCELLED,
            'cancelled_at' => now(),
        ];

        if ($immediately) {
            $data['ends_at'] = now();
        }

        $this->update($data);

        return $this;
    }

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
    public function getUsedStorageInBytes(): int
    {
        return $this->getDatabaseUsageBytes() + $this->getS3UsageBytes();
    }

    public function getStorageUsagePercentage(): float
    {
        $limit = $this->getPlanFeatures()['max_storage'] ?? 0;

        if ($limit == -1) {
            return 0;
        }

        $limitBytes = $limit * 1024 * 1024;

        if ($limitBytes <= 0) {
            return 100;
        }

        $usedBytes = $this->getUsedStorageInBytes();

        return round(($usedBytes / $limitBytes) * 100, 2);
    }

    public function getS3UsageBytes(): int
    {
        // 1. Check/Load relationship
        if (! $this->relationLoaded('tenant')) {
            $this->load('tenant');
        }

        if (! $this->tenant) {
            return 0;
        }

        return $this->tenant->run(function () {

            if (config('filesystems.disks.s3') === null) {
                return 0;
            }

            $size = 0;
            try {
                // Get all files in the tenant's root
                $files = Storage::disk('s3')->allFiles('/');

                foreach ($files as $file) {
                    // EXCLUSION LOGIC:
                    // Skip if the file is inside the livewire-tmp directory
                    if (str_starts_with($file, 'livewire-tmp')) {
                        continue;
                    }

                    $size += Storage::disk('s3')->size($file);
                }
            } catch (\Exception $e) {
                return 0;
            }

            return $size;
        });
    }

    /**
     * Calculate size of Database rows for this tenant.
     * Database Agnostic (Postgres, MySQL, SQLite)
     */
    public function getDatabaseUsageBytes(): int
    {
        $tenantId = $this->tenant_id;
        $totalSize = 0;

        $tablesToCheck = [
            'users',
            'patients',
            'appointments',
            'admissions',
            'prescriptions',
            'prescription_items',
            'medications',
            'medical_records',
            'medical_record_attachments',
            'lab_requests',
            'lab_results',
            'lab_result_attachments',
            'lab_test_definitions',
            'dispensations',
            'invoices',
            'vitals',
            'nurse_care_reports',
            'user_shifts',
            'user_activities',
            'activity_logs',
            'supplies',
            'supply_usages',
            'wards',
            'beds',
            'bed_types',
            'revenue_summaries',
            'notifications',
            'feed_backs',
            'files',
            'procedure_kits',
            'demo_requests',
            'subscriptions',
        ];

        // Determine the database driver
        $connection = DB::connection();
        $driver = $connection->getDriverName();

        foreach ($tablesToCheck as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            $size = 0;

            try {
                if ($driver === 'pgsql') {
                    // PostgreSQL: Use pg_column_size on the entire row
                    // We need to pass the table name as a parameter to pg_column_size
                    $size = DB::table($table)
                        ->where('tenant_id', $tenantId)
                        ->selectRaw("SUM(pg_column_size({$table}.*)) as total_size")
                        ->value('total_size');

                } elseif ($driver === 'mysql' || $driver === 'mariadb') {
                    // MySQL: Sum the length of all columns as an approximation
                    $columns = Schema::getColumnListing($table);

                    if (! empty($columns)) {
                        $sumQuery = collect($columns)
                            ->map(fn ($col) => "LENGTH(COALESCE(`$col`, ''))")
                            ->join(' + ');

                        $size = DB::table($table)
                            ->where('tenant_id', $tenantId)
                            ->sum(DB::raw($sumQuery));
                    }

                } elseif ($driver === 'sqlite') {
                    // SQLite: Similar to MySQL
                    $columns = Schema::getColumnListing($table);

                    if (! empty($columns)) {
                        $sumQuery = collect($columns)
                            ->map(fn ($col) => "length(coalesce(\"$col\", ''))")
                            ->join(' + ');

                        $size = DB::table($table)
                            ->where('tenant_id', $tenantId)
                            ->sum(DB::raw($sumQuery));
                    }
                }

                $totalSize += (int) ($size ?? 0);

            } catch (\Exception $e) {
                // Log but don't fail - continue with other tables
                Log::warning("Failed to calculate size for table {$table}: ".$e->getMessage());
            }
        }

        return $totalSize;
    }

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
