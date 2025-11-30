// database/migrations/2025_01_01_000012_create_lab_test_definitions_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_test_definitions', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('test_name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0.00);

            $table->string('test_code')->nullable();
            $table->string('normal_range')->nullable();
            $table->string('units')->nullable();

            $table->timestamps();
            $table->index('tenant_id', 'lab_test_definitions_tenant_id_index');
            $table->unique(['tenant_id', 'test_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_test_definitions');
    }
};
