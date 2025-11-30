
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('patient_uid')->unique(); // Unique ID for patient per tenant
            $table->string('first_name');
            $table->string('last_name');
            $table->integer('age');
            $table->date('dob')->nullable();
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('blood_group')->nullable(); // e.g., A+, O-
            $table->string('phone')->nullable();
            $table->string('email')->nullable(); // Patient's email
            $table->string('address')->nullable();
            $table->string('insurance_provider')->nullable();
            $table->string('insurance_policy_number')->nullable();
            $table->string('id_document_path')->nullable(); // Path to uploaded ID scan
            $table->string('referral_note_path')->nullable(); // Path to uploaded referral note
            $table->timestamps();
            $table->boolean('is_admitted_approve')->default(false);
            $table->unique(['tenant_id', 'patient_uid']);
            $table->index(['tenant_id', 'first_name', 'last_name']);
            $table->unique(['tenant_id', 'phone']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
