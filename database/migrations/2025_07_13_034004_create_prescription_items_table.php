// database/migrations/2025_01_01_000011_create_prescription_items_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained('prescriptions')->cascadeOnDelete();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('medication_id')->constrained('medications')->cascadeOnDelete();
            $table->string('dosage');
            $table->string('frequency');
            $table->string('duration');
            $table->integer('quantity_prescribed');
            $table->integer('dispensed_quantity')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('tenant_id', 'prescription_items_tenant_id_index');
            $table->index(['tenant_id', 'prescription_id', 'medication_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescription_items');
    }
};
