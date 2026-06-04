<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tenant-only tables for testing. These tables exist only in tenant
 * databases and NOT in the central DB, so they need to be created
 * separately from RefreshDatabase which runs central migrations.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('patients')) return;

        Schema::create('departments', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('code')->unique();
            $table->text('description')->nullable(); $table->string('phone_extension', 20)->nullable();
            $table->string('type')->default('clinical')->index(); $table->boolean('is_active')->default(true)->index();
            $table->foreignId('head_of_department_id')->nullable(); $table->timestamps();
        });
        Schema::create('bed_types', function (Blueprint $table) {
            $table->id(); $table->string('name', 100); $table->text('description')->nullable();
            $table->decimal('daily_rate', 10, 2)->nullable(); $table->integer('capacity')->default(1); $table->timestamps();
        });
        Schema::create('wards', function (Blueprint $table) {
            $table->id(); $table->string('name', 100); $table->string('code', 20)->unique();
            $table->text('description')->nullable(); $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('floor', 20)->nullable(); $table->integer('capacity')->default(0);
            $table->boolean('is_active')->default(true)->index(); $table->timestamps();
        });
        Schema::create('patients', function (Blueprint $table) {
            $table->id(); $table->string('patient_uid')->unique()->index();
            $table->string('first_name', 100); $table->string('last_name', 100);
            $table->string('email')->nullable(); $table->string('phone', 30)->nullable();
            $table->date('date_of_birth')->nullable(); $table->string('gender')->nullable()->index();
            $table->string('blood_type', 10)->nullable(); $table->boolean('is_admitted_approve')->default(false);
            $table->text('allergies')->nullable(); $table->text('address')->nullable();
            $table->timestamp('last_visit_date')->nullable()->index();
            $table->index(['first_name', 'last_name']); $table->timestamps();
        });
        Schema::create('beds', function (Blueprint $table) {
            $table->id(); $table->foreignId('ward_id')->constrained()->cascadeOnDelete();
            $table->string('code', 20); $table->foreignId('bed_type_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 20)->default('available')->index();
            $table->unique(['ward_id', 'code']); $table->timestamps();
        });
        Schema::create('medications', function (Blueprint $table) {
            $table->id(); $table->string('name', 200)->index(); $table->string('strength', 50)->nullable();
            $table->string('dosage_units', 50)->nullable(); $table->integer('min_stock_level')->default(0);
            $table->integer('total_stock')->default(0); $table->text('description')->nullable();
            $table->decimal('unit_cost', 10, 2)->nullable(); $table->boolean('is_active')->default(true)->index(); $table->timestamps();
        });
        Schema::create('lab_test_definitions', function (Blueprint $table) {
            $table->id(); $table->string('test_name', 200)->index(); $table->string('code', 50)->nullable()->unique();
            $table->decimal('price', 10, 2)->nullable(); $table->string('units', 50);
            $table->text('description')->nullable(); $table->boolean('is_active')->default(true)->index(); $table->timestamps();
        });
        Schema::create('supplies', function (Blueprint $table) {
            $table->id(); $table->string('name', 200)->index(); $table->string('unit_of_measure', 50)->default('Unit');
            $table->integer('current_stock')->default(0); $table->integer('min_stock_level')->default(10);
            $table->decimal('cost_per_unit', 10, 2)->nullable(); $table->string('category', 100)->default('General')->index();
            $table->date('last_restocked')->nullable(); $table->timestamps();
        });
        Schema::create('procedure_kits', function (Blueprint $table) {
            $table->id(); $table->string('name', 200)->index(); $table->text('description')->nullable();
            $table->integer('current_stock')->default(0); $table->integer('min_stock_level')->default(1);
            $table->decimal('unit_cost', 10, 2)->nullable(); $table->string('category', 100)->nullable()->index();
            $table->json('contents')->nullable(); $table->timestamps();
        });
        Schema::create('admissions', function (Blueprint $table) {
            $table->id(); $table->foreignId('patient_id')->constrained()->cascadeOnDelete()->index();
            $table->foreignId('bed_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('attending_doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('admission_date')->index()->nullable(); $table->date('discharge_date')->nullable();
            $table->string('status')->default('pending')->index(); $table->text('reason_for_admission')->nullable();
            $table->decimal('observation_fee', 10, 2)->nullable(); $table->timestamps();
        });
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id(); $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('consultation_id')->nullable(); $table->date('record_date')->index();
            $table->text('chief_complaint')->nullable(); $table->text('history_of_present_illness')->nullable();
            $table->text('physical_exam')->nullable(); $table->text('diagnosis'); $table->text('treatment_plan');
            $table->text('notes')->nullable(); $table->timestamps();
        });
        Schema::create('vitals', function (Blueprint $table) {
            $table->id(); $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->dateTime('recorded_at')->index(); $table->decimal('temperature', 4, 1)->nullable();
            $table->integer('systolic_bp')->nullable(); $table->integer('diastolic_bp')->nullable();
            $table->integer('heart_rate')->nullable(); $table->integer('oxygen_saturation')->nullable();
            $table->integer('respiratory_rate')->nullable(); $table->boolean('is_abnormal')->default(false)->index();
            $table->text('notes')->nullable(); $table->timestamps();
        });
        Schema::create('appointments', function (Blueprint $table) {
            $table->id(); $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('appointment_date')->index(); $table->time('appointment_time');
            $table->string('status', 20)->index(); $table->decimal('consultation_fee', 10, 2)->nullable()->default(0);
            $table->unsignedInteger('queue_position')->nullable(); $table->timestamps();
        });
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id(); $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->date('prescription_date')->index(); $table->string('status')->default('new')->index();
            $table->text('notes')->nullable(); $table->decimal('total_amount', 10, 2)->nullable(); $table->timestamps();
        });
        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id(); $table->foreignId('prescription_id')->constrained()->cascadeOnDelete()->index();
            $table->foreignId('medication_id')->constrained()->cascadeOnDelete()->index();
            $table->string('dosage', 50); $table->string('frequency', 50); $table->string('duration', 50);
            $table->string('drug_name'); $table->integer('quantity');
            $table->decimal('unit_price', 10, 2)->nullable(); $table->text('instructions')->nullable(); $table->timestamps();
        });
        Schema::create('lab_requests', function (Blueprint $table) {
            $table->id(); $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('test_definition_id')->constrained('lab_test_definitions')->cascadeOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('consultation_id')->nullable(); $table->dateTime('request_date')->index();
            $table->string('status')->default('Pending')->index(); $table->string('urgency')->nullable()->index();
            $table->text('reason_for_test')->nullable(); $table->timestamps();
        });
        Schema::create('lab_results', function (Blueprint $table) {
            $table->id(); $table->foreignId('lab_request_id')->constrained()->cascadeOnDelete()->unique();
            $table->string('status')->default('In Progress')->index(); $table->date('result_date')->index();
            $table->text('results_text')->nullable(); $table->text('analysis_comments')->nullable(); $table->timestamps();
        });
        Schema::create('nurse_care_reports', function (Blueprint $table) {
            $table->id(); $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('nurse_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('report_date')->index(); $table->string('shift_type')->index();
            $table->text('care_details'); $table->text('observations')->nullable(); $table->timestamps();
        });
        Schema::create('dispensations', function (Blueprint $table) {
            $table->id(); $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('prescription_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('dispensed_by')->constrained('users')->cascadeOnDelete();
            $table->dateTime('dispensed_at')->index(); $table->decimal('total_amount', 10, 2)->nullable();
            $table->text('notes')->nullable(); $table->timestamps();
        });
        Schema::create('dispensation_items', function (Blueprint $table) {
            $table->id(); $table->foreignId('dispensation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('medication_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity'); $table->decimal('unit_price', 10, 2); $table->timestamps();
        });
        Schema::create('invoices', function (Blueprint $table) {
            $table->id(); $table->string('invoice_number', 50)->unique()->index();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->date('invoice_date'); $table->date('due_date')->nullable()->index();
            $table->decimal('subtotal', 10, 2)->default(0); $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0); $table->string('status')->default('pending')->index();
            $table->date('paid_at')->nullable(); $table->text('notes')->nullable(); $table->timestamps();
        });
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id(); $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->string('item_type'); $table->unsignedBigInteger('item_id')->nullable();
            $table->string('description'); $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2); $table->decimal('total_price', 10, 2); $table->timestamps();
        });
        Schema::create('supply_usages', function (Blueprint $table) {
            $table->id(); $table->foreignId('supply_id')->constrained()->cascadeOnDelete();
            $table->foreignId('used_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('patient_id')->nullable()->constrained()->nullOnDelete()->index();
            $table->integer('used_quantity'); $table->dateTime('used_at')->index();
            $table->text('purpose')->nullable(); $table->timestamps();
        });
        Schema::create('batches', function (Blueprint $table) {
            $table->id(); $table->morphs('batchable'); $table->string('batch_number', 100)->unique()->index();
            $table->date('expiry_date')->index(); $table->integer('initial_quantity'); $table->integer('current_quantity');
            $table->decimal('purchase_price', 10, 2)->nullable(); $table->date('purchase_date')->nullable();
            $table->string('supplier', 200)->nullable(); $table->string('status')->default('active')->index(); $table->timestamps();
        });
        Schema::create('feedback', function (Blueprint $table) {
            $table->id(); $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('subject', 200); $table->text('message'); $table->string('category', 100)->index();
            $table->string('status')->default('pending')->index(); $table->text('response')->nullable();
            $table->foreignId('responder_id')->nullable()->constrained('users')->nullOnDelete(); $table->timestamps();
        });
        Schema::create('emergency_cases', function (Blueprint $table) {
            $table->id(); $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attending_doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('triage_nurse_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('arrival_mode')->nullable(); $table->string('chief_complaint');
            $table->text('primary_assessment')->nullable(); $table->string('triage_level')->default('normal')->index();
            $table->string('status')->default('triage')->index(); $table->string('disposition')->nullable();
            $table->foreignId('admission_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('arrived_at')->index(); $table->dateTime('triaged_at')->nullable();
            $table->dateTime('stabilized_at')->nullable(); $table->dateTime('discharged_at')->nullable();
            $table->decimal('emergency_fee', 10, 2)->default(0); $table->text('treatment_notes')->nullable();
            $table->text('discharge_instructions')->nullable(); $table->timestamps();
        });
        Schema::create('surgeries', function (Blueprint $table) {
            $table->id(); $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('surgeon_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('anesthetist_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('bed_id')->nullable()->constrained()->nullOnDelete();
            $table->string('surgery_name'); $table->text('description')->nullable();
            $table->dateTime('scheduled_at')->index(); $table->dateTime('completed_at')->nullable();
            $table->integer('estimated_duration_minutes')->nullable(); $table->string('urgency')->default('normal')->index();
            $table->string('status')->default('scheduled')->index(); $table->decimal('surgery_fee', 10, 2)->default(0);
            $table->text('notes')->nullable(); $table->text('complications')->nullable(); $table->timestamps();
        });
        Schema::create('expenditures', function (Blueprint $table) {
            $table->id(); $table->string('category')->index(); $table->string('description');
            $table->decimal('amount', 12, 2); $table->date('expenditure_date')->index();
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->string('vendor')->nullable(); $table->string('reference_number')->nullable();
            $table->text('notes')->nullable(); $table->timestamps();
        });
// user_shifts is created by a central migration (2026_05_24_000010)
        Schema::create('activity_log', function (Blueprint $table) {
            $table->id(); $table->string('log_name')->nullable()->index(); $table->text('description');
            $table->nullableMorphs('subject'); $table->string('event')->nullable(); $table->nullableMorphs('causer');
            $table->json('attribute_changes')->nullable(); $table->json('properties')->nullable(); $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach ([
'expenditures','surgeries','emergency_cases',
            'feedback','batches','supply_usages','invoice_items','invoices',
            'dispensation_items','dispensations','nurse_care_reports','lab_results','lab_requests',
            'prescription_items','prescriptions','appointments','vitals','medical_records',
            'admissions','procedure_kits','supplies','lab_test_definitions','medications',
            'beds','patients','wards','bed_types','departments',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
