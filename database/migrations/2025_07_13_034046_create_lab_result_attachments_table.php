// database/migrations/2025_01_01_000015_create_lab_result_attachments_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_result_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('lab_result_id')->constrained('lab_results')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type', 50); // e.g., 'image/jpeg', 'application/pdf'
            $table->timestamps();

            $table->index(['tenant_id', 'lab_result_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_result_attachments');
    }
};
