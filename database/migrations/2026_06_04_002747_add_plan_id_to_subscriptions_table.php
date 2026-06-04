<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->foreignId('plan_id')->nullable()->after('tenant_id')->constrained('plans')->nullOnDelete();
            $table->boolean('auto_renew')->default(true)->after('status');
            $table->timestamp('cancelled_at')->nullable()->after('auto_renew');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
            $table->dropColumn(['plan_id', 'auto_renew', 'cancelled_at']);
        });
    }
};
