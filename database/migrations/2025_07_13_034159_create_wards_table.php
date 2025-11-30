
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wards', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name'); // E.g., 'Ward A', 'Pediatric Ward'
            $table->string('ward_number')->nullable(); // Optional: A specific number or code
            $table->foreignId('department_id')->nullable()->constrained('departments')->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'name']);
            // If ward_number is truly unique across the tenant, uncomment the line below.
            // If it's unique only within a department, create a combined unique index.
            $table->unique(['tenant_id', 'ward_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wards');
    }
};
