<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The landlord columns (is_active, suspended_at, trial_ends_at,
 * settings_json) were added to `tenants` AFTER stancl/tenancy had already
 * persisted the same keys inside the `data` JSON blob.
 *
 * VirtualColumn decodes `data` back into model attributes on every hydrate,
 * which overwrote the real columns — so suspending a tenant wrote the column
 * and then read the stale JSON value back, making the flag impossible to
 * change. Drop the duplicated keys, keeping the virtual ones (name,
 * tenancy_db_name, …) intact.
 */
return new class extends Migration
{
    /**
     * Keys that now live in real columns and must not be duplicated in data.
     *
     * @var array<int, string>
     */
    private array $legacyKeys = [
        'is_active',
        'suspended_at',
        'trial_ends_at',
        'settings_json',
        'created_at',
        'updated_at',
    ];

    public function up(): void
    {
        $connection = DB::connection();

        $connection->table('tenants')->orderBy('id')->each(function ($row) use ($connection) {
            $data = is_string($row->data) ? json_decode($row->data, true) : $row->data;

            if (! is_array($data)) {
                return;
            }

            $cleaned = array_diff_key($data, array_flip($this->legacyKeys));

            if ($cleaned === $data) {
                return;
            }

            $connection->table('tenants')
                ->where('id', $row->id)
                ->update(['data' => json_encode($cleaned)]);
        });
    }

    public function down(): void
    {
        // Data normalisation is not reversible; the columns remain the source
        // of truth.
    }
};
