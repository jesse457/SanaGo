
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supply_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('supply_id')->constrained('supplies')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('patient_id')->nullable()->constrained('patients')->cascadeOnDelete();
            $table->integer('quantity_used');
            $table->timestamp('usage_date')->useCurrent();
            $table->timestamps();
            $table->index('tenant_id', 'supply_usages_tenant_id_index');
            $table->index(['tenant_id', 'supply_id', 'user_id', 'patient_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supply_usages');
    }
};
