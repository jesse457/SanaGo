// database/migrations/2025_01_01_000007_create_medical_record_attachments_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_record_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('medical_record_id')->constrained('medical_records')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type', 50); // e.g., 'application/pdf', 'image/jpeg'
            $table->timestamps();
            $table->index('tenant_id', 'medical_record_attachments_tenant_id_index');
            $table->index(['tenant_id', 'medical_record_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_record_attachments');
    }
};
