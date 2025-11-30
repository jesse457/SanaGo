
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beds', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('ward_id')->nullable()->constrained('wards')->cascadeOnDelete(); // Link to Ward
            $table->foreignId('bed_type_id')->constrained('bed_types')->cascadeOnDelete();
            $table->string('bed_number'); // Unique identifier for the bed within its tenant (e.g., 'A-101')
            $table->boolean('is_occupied')->default(false);
            $table->timestamps();
            $table->index('tenant_id', 'beds_tenant_id_index');
            // Bed number must be unique per tenant
            $table->unique(['tenant_id', 'bed_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beds');
    }
};
