<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('data');
            $table->timestamp('suspended_at')->nullable()->after('is_active');
            $table->date('trial_ends_at')->nullable()->after('suspended_at');
            $table->json('settings_json')->nullable()->after('trial_ends_at');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'suspended_at', 'trial_ends_at', 'settings_json']);
        });
    }
};
