
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispensations', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('prescription_item_id')->constrained('prescription_items')->cascadeOnDelete();
            $table->foreignId('pharmacist_id')->constrained('users')->cascadeOnDelete(); // Who dispensed
            $table->integer('quantity_issued');
            $table->string('batch_number')->nullable();
            $table->decimal('total_price', 10, 2); // Calculated price of this dispensation
            $table->timestamp('dispensed_at')->useCurrent();
            $table->timestamps();
            $table->index('tenant_id', 'dispensations_tenant_id_index');
            $table->index(['tenant_id', 'prescription_item_id', 'pharmacist_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispensations');
    }
};
