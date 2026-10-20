<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the capability columns a plan needs to actually gate tenant features
 * and enforce quotas. `features_json` stays the marketing bullet list; the
 * new `feature_flags_json` map is what the application reads.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->string('currency', 3)->default('XAF')->after('yearly_price');
            $table->integer('max_patients_per_tenant')->nullable()->after('max_users_per_tenant');
            $table->integer('trial_days')->default(14)->after('storage_gb');
            $table->integer('grace_days')->default(3)->after('trial_days');
            $table->boolean('is_featured')->default(false)->after('is_active');
            $table->json('feature_flags_json')->nullable()->after('features_json');
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn([
                'currency',
                'max_patients_per_tenant',
                'trial_days',
                'grace_days',
                'is_featured',
                'feature_flags_json',
            ]);
        });
    }
};
