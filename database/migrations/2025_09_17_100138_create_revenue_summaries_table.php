<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('revenue_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->date('transaction_date');

            $table->decimal('medication_revenue', 15, 2)->default(0);
            $table->decimal('appointment_revenue', 15, 2)->default(0);
            $table->decimal('lab_revenue', 15, 2)->default(0);
            $table->decimal('admission_revenue', 15, 2)->default(0);
            $table->decimal('bed_fee_revenue', 15, 2)->default(0);
            $table->timestamps();

            // --- OPTIMIZATION CHANGES ---

            // The unique index MUST include the tenant_id to correctly scope uniqueness.
            // This allows the use of updateOrCreate() for high performance per tenant.
            $table->unique(['tenant_id', 'patient_id', 'transaction_date'], 'revenue_patient_date_unique');

            // For fetching a specific tenant's revenue over a date range.
            // This composite index will allow for very fast reporting queries.
            $table->index(['tenant_id', 'transaction_date'], 'revenue_tenant_date_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revenue_summaries');
    }
};
