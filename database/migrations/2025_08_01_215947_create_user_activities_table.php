// File: database/migrations/tenants/2025_08_01_105747_create_user_activities_table.php

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the tenant migrations.
     */
    public function up(): void
    {
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('activity_type');
            $table->string('description', 500);
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->json('properties')->nullable();
            $table->index('tenant_id', 'user_activities_tenant_id_index');
            $table->index(['tenant_id', 'user_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the tenant migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activities');
    }
};
