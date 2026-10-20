<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Turns `subscriptions` into a real per-tenant lifecycle record:
 * one current subscription per tenant (historical periods are kept as
 * invoices), a trial window, cancellation-at-period-end, per-tenant limit
 * and feature overrides, and the currency amounts are expressed in.
 *
 * Existing duplicate rows are collapsed to the most recent one per tenant
 * before the unique index is added; the older rows predate the invoice
 * ledger that now records billing history.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('currency', 3)->default('XAF')->after('price');
            $table->date('trial_ends_at')->nullable()->after('end_date');
            $table->timestamp('last_renewed_at')->nullable()->after('auto_renew');
            $table->boolean('cancel_at_period_end')->default(false)->after('cancelled_at');
            $table->json('limits_json')->nullable()->after('features_json');
            $table->json('feature_overrides_json')->nullable()->after('limits_json');
            $table->text('notes')->nullable()->after('feature_overrides_json');
        });

        // Collapse duplicates so the tenant -> subscription relation is
        // deterministic (Tenant::subscription() is a hasOne).
        $duplicateTenants = DB::connection()
            ->table('subscriptions')
            ->select('tenant_id')
            ->whereNotNull('tenant_id')
            ->groupBy('tenant_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('tenant_id');

        foreach ($duplicateTenants as $tenantId) {
            $keep = DB::connection()
                ->table('subscriptions')
                ->where('tenant_id', $tenantId)
                ->orderByDesc('start_date')
                ->orderByDesc('id')
                ->value('id');

            DB::connection()
                ->table('subscriptions')
                ->where('tenant_id', $tenantId)
                ->where('id', '!=', $keep)
                ->delete();
        }

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->unique('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropUnique(['tenant_id']);
            $table->dropColumn([
                'currency',
                'trial_ends_at',
                'last_renewed_at',
                'cancel_at_period_end',
                'limits_json',
                'feature_overrides_json',
                'notes',
            ]);
        });
    }
};
