<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tenants are archived rather than hard-deleted from the panel.
 *
 * stancl/tenancy drops the tenant database from its `deleted` model event,
 * which also fires on a soft delete — so a `SoftDeletes` trait would destroy
 * data on "delete". An explicit `archived_at` marker keeps the record (and
 * its data) recoverable, while `forceDelete()` remains available for a
 * deliberate, confirmed purge.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->timestamp('archived_at')->nullable()->after('settings_json');
            $table->string('archived_reason')->nullable()->after('archived_at');
            $table->timestamp('last_seen_at')->nullable()->after('archived_reason');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['archived_at', 'archived_reason', 'last_seen_at']);
        });
    }
};
