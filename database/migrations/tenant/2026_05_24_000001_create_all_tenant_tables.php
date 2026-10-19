<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Reference data (no FKs)
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('phone_extension', 20)->nullable();
            $table->string('type')->default('clinical')->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('bed_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->decimal('daily_rate', 10, 2)->nullable();
            $table->integer('capacity')->default(1);
            $table->timestamps();
        });

        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200)->index();
            $table->string('strength', 50)->nullable();
            $table->string('dosage_units', 50)->nullable();
            $table->integer('min_stock_level')->default(0);
            $table->integer('total_stock')->default(0);
            $table->text('description')->nullable();
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('lab_test_definitions', function (Blueprint $table) {
            $table->id();
            $table->string('test_name', 200)->index();
            $table->string('code', 50)->nullable()->unique();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('units', 50);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('supplies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200)->index();
            $table->string('unit_of_measure', 50)->default('Unit');
            $table->integer('current_stock')->default(0);
            $table->integer('min_stock_level')->default(10);
            $table->decimal('cost_per_unit', 10, 2)->nullable();
            $table->string('category', 100)->default('General')->index();
            $table->date('last_restocked')->nullable();
            $table->index(['current_stock', 'min_stock_level']);
            $table->timestamps();
        });

        Schema::create('procedure_kits', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200)->index();
            $table->text('description')->nullable();
            $table->integer('current_stock')->default(0);
            $table->integer('min_stock_level')->default(1);
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->string('category', 100)->nullable()->index();
            $table->json('contents')->nullable();
            $table->timestamps();
        });

        // 2. wards (FK -> departments)
        Schema::create('wards', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 20)->unique();
            $table->text('description')->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('floor', 20)->nullable();
            $table->integer('capacity')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        // 3. patients (no FKs)
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('patient_uid')->unique()->index();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable()->index();
            $table->string('blood_type', 10)->nullable();
            $table->boolean('is_admitted_approve')->default(false);
            $table->text('allergies')->nullable();
            $table->text('address')->nullable();
            $table->timestamp('last_visit_date')->nullable()->index();

            $table->index(['first_name', 'last_name']);
            $table->timestamps();
        });

        // 4. users (FK -> departments)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone_number')->nullable();
            $table->string('address')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->boolean('can_assign_shift')->nullable()->default(false);
            $table->unsignedBigInteger('role_id')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('password');
            $table->boolean('is_active')->default(true)->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->cascadeOnDelete();
            $table->date('hire_date')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // 5. beds (FK -> wards, bed_types)
        Schema::create('beds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ward_id')->constrained()->cascadeOnDelete();
            $table->string('code', 20);
            $table->foreignId('bed_type_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 20)->default('available')->index();
            $table->unique(['ward_id', 'code']);
            $table->timestamps();
        });

        // 6. user_shifts (FK -> users)
        Schema::create('user_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('shift_date');
            $table->string('shift_type')->index();
            $table->time('start_time');
            $table->time('end_time');
            $table->unique(['user_id', 'shift_date', 'shift_type']);
            $table->index('shift_date');
            $table->timestamps();
        });

        // 7. admissions (FK -> patients, beds, users)
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete()->index();
            $table->foreignId('bed_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('attending_doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('admission_date')->index()->nullable();
            $table->date('discharge_date')->nullable();
            $table->string('status')->default('pending')->index();
            $table->text('reason_for_admission')->nullable();
            $table->decimal('observation_fee', 10, 2)->nullable();
            $table->timestamps();
        });

        // 8. medical_records (FK -> patients, users)
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('consultation_id')->nullable();
            $table->date('record_date')->index();
            $table->text('chief_complaint')->nullable();
            $table->text('history_of_present_illness')->nullable();
            $table->text('physical_exam')->nullable();
            $table->text('diagnosis');
            $table->text('treatment_plan');
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();
            $table->index(['patient_id', 'record_date']);
            $table->timestamps();
        });

        // 9. vitals (FK -> patients, users)
        Schema::create('vitals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->dateTime('recorded_at')->index();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->integer('systolic_bp')->nullable();
            $table->integer('diastolic_bp')->nullable();
            $table->integer('heart_rate')->nullable();
            $table->integer('oxygen_saturation')->nullable();
            $table->integer('respiratory_rate')->nullable();
            $table->boolean('is_abnormal')->default(false)->index();
            $table->text('notes')->nullable();
            $table->index(['patient_id', 'recorded_at']);
            $table->timestamps();
        });

        // 10. nurse_care_reports (FK -> patients, users)
        Schema::create('nurse_care_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('nurse_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('report_date')->index();
            $table->string('shift_type')->index();
            $table->text('care_details');
            $table->text('observations')->nullable();
            $table->index(['patient_id', 'report_date']);
            $table->timestamps();
        });

        // 11. prescriptions (FK -> patients, users)
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->date('prescription_date')->index();
            $table->string('status')->default('new')->index();
            $table->text('notes')->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->index(['patient_id', 'status']);
            $table->timestamps();
        });

        // 12. lab_requests (FK -> patients, lab_test_definitions, users, medical_records)
        Schema::create('lab_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('test_definition_id')->constrained('lab_test_definitions')->cascadeOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('consultation_id')->nullable()->constrained('medical_records')->nullOnDelete();
            $table->dateTime('request_date')->index();
            $table->string('status')->default('Pending')->index();
            $table->string('urgency')->nullable()->index();
            $table->text('reason_for_test')->nullable();
            $table->index(['patient_id', 'status']);
            $table->timestamps();
        });

        // 13. prescription_items (FK -> prescriptions, medications)
        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained()->cascadeOnDelete()->index();
            $table->foreignId('medication_id')->constrained()->cascadeOnDelete()->index();
            $table->string('dosage', 50);
            $table->string('frequency', 50);
            $table->string('duration', 50);
            $table->string('drug_name');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->text('instructions')->nullable();
            $table->timestamps();
        });

        // 14. lab_results (FK -> lab_requests)
        Schema::create('lab_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_request_id')->constrained()->cascadeOnDelete()->unique();
            $table->string('status')->default('In Progress')->index();
            $table->date('result_date')->index();
            $table->text('results_text')->nullable();
            $table->text('analysis_comments')->nullable();
            $table->timestamps();
        });

        // 15. dispensations (FK -> patients, prescriptions, users)
        Schema::create('dispensations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('prescription_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('dispensed_by')->constrained('users')->cascadeOnDelete();
            $table->dateTime('dispensed_at')->index();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->index(['patient_id', 'dispensed_at']);
            $table->timestamps();
        });

        // 16. dispensation_items (FK -> dispensations, medications)
        Schema::create('dispensation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispensation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('medication_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->timestamps();
        });

        // 17. appointments (FK -> patients, users)
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('appointment_date')->index();
            $table->time('appointment_time');
            $table->string('status', 20)->index();
            $table->string('reason_for_visit')->nullable();
            $table->decimal('consultation_fee', 10, 2)->nullable()->default(0);
            $table->unsignedInteger('queue_position')->nullable();
            $table->timestamps();
        });

        // 18. supply_usages (FK -> supplies, users, patients)
        Schema::create('supply_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supply_id')->constrained()->cascadeOnDelete();
            $table->foreignId('used_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('patient_id')->nullable()->constrained()->nullOnDelete()->index();
            $table->integer('used_quantity');
            $table->dateTime('used_at')->index();
            $table->text('purpose')->nullable();
            $table->index(['supply_id', 'used_at']);
            $table->timestamps();
        });

        // 19. batches (polymorphic)
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->morphs('batchable');
            $table->string('batch_number', 100)->unique()->index();
            $table->date('expiry_date')->index();
            $table->integer('initial_quantity');
            $table->integer('current_quantity');
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->date('purchase_date')->nullable();
            $table->string('supplier', 200)->nullable();
            $table->string('status')->default('active')->index();
            $table->text('notes')->nullable();
            $table->index(['current_quantity', 'expiry_date']);
            $table->timestamps();
        });

        // 20. feedback (FK -> users)
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('subject', 200);
            $table->text('message');
            $table->string('category', 100)->index();
            $table->string('status')->default('pending')->index();
            $table->text('response')->nullable();
            $table->foreignId('responder_id')->nullable()->constrained('users')->nullOnDelete();
            $table->index('created_at');
            $table->timestamps();
        });

        // 21. invoices (FK -> patients)
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique()->index();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->date('invoice_date');
            $table->date('due_date')->nullable()->index();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->string('status')->default('pending')->index();
            $table->date('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->index(['patient_id', 'invoice_date']);
            $table->timestamps();
        });

        // 22. invoice_items (FK -> invoices)
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->string('item_type');
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('description');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->timestamps();
        });

        // 23. emergency_cases (FK -> patients, users, admissions)
        Schema::create('emergency_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attending_doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('triage_nurse_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('arrival_mode')->nullable();
            $table->string('chief_complaint');
            $table->text('primary_assessment')->nullable();
            $table->string('triage_level')->default('normal')->index();
            $table->string('status')->default('triage')->index();
            $table->string('disposition')->nullable();
            $table->foreignId('admission_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('arrived_at')->index();
            $table->dateTime('triaged_at')->nullable();
            $table->dateTime('stabilized_at')->nullable();
            $table->dateTime('discharged_at')->nullable();
            $table->decimal('emergency_fee', 10, 2)->default(0);
            $table->text('treatment_notes')->nullable();
            $table->text('discharge_instructions')->nullable();
            $table->timestamps();
        });

        // 24. surgeries (FK -> patients, users, beds)
        Schema::create('surgeries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('surgeon_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('anesthetist_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('bed_id')->nullable()->constrained()->nullOnDelete();
            $table->string('surgery_name');
            $table->text('description')->nullable();
            $table->dateTime('scheduled_at')->index();
            $table->dateTime('completed_at')->nullable();
            $table->integer('estimated_duration_minutes')->nullable();
            $table->string('urgency')->default('normal')->index();
            $table->string('status')->default('scheduled')->index();
            $table->decimal('surgery_fee', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->text('complications')->nullable();
            $table->timestamps();
        });

        // 25. expenditures (FK -> users)
        Schema::create('expenditures', function (Blueprint $table) {
            $table->id();
            $table->string('category')->index();
            $table->string('description');
            $table->decimal('amount', 12, 2);
            $table->date('expenditure_date')->index();
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->string('vendor')->nullable();
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 26. spatie/laravel-permission tables
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->unique(['name', 'guard_name']);
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->unique(['name', 'guard_name']);
            $table->timestamps();
        });

        Schema::create('model_has_permissions', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->primary(['permission_id', 'model_id', 'model_type']);
            $table->index(['model_id', 'model_type']);
        });

        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->primary(['role_id', 'model_id', 'model_type']);
            $table->index(['model_id', 'model_type']);
        });

        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->primary(['permission_id', 'role_id']);
        });

        // 27. activity_log (spatie/laravel-activitylog)
        Schema::create('activity_log', function (Blueprint $table) {
            $table->id();
            $table->string('log_name')->nullable()->index();
            $table->text('description');
            $table->nullableMorphs('subject');
            $table->string('event')->nullable();
            $table->nullableMorphs('causer');
            $table->json('attribute_changes')->nullable();
            $table->json('properties')->nullable();
            $table->timestamps();
        });

        // 28. media (spatie/laravel-medialibrary)
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->morphs('model');
            $table->char('uuid', 36)->nullable()->unique();
            $table->string('collection_name');
            $table->string('name');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->string('disk');
            $table->string('conversions_disk')->nullable();
            $table->unsignedBigInteger('size');
            $table->json('manipulations');
            $table->json('custom_properties');
            $table->json('generated_conversions');
            $table->json('responsive_images');
            $table->unsignedInteger('order_column')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
        Schema::dropIfExists('activity_log');
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('model_has_permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('expenditures');
        Schema::dropIfExists('surgeries');
        Schema::dropIfExists('emergency_cases');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('feedback');
        Schema::dropIfExists('batches');
        Schema::dropIfExists('supply_usages');
        Schema::dropIfExists('procedure_kits');
        Schema::dropIfExists('supplies');
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('dispensation_items');
        Schema::dropIfExists('dispensations');
        Schema::dropIfExists('lab_results');
        Schema::dropIfExists('prescription_items');
        Schema::dropIfExists('lab_requests');
        Schema::dropIfExists('prescriptions');
        Schema::dropIfExists('nurse_care_reports');
        Schema::dropIfExists('vitals');
        Schema::dropIfExists('medical_records');
        Schema::dropIfExists('admissions');
        Schema::dropIfExists('user_shifts');
        Schema::dropIfExists('beds');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('patients');
        Schema::dropIfExists('wards');
        Schema::dropIfExists('lab_test_definitions');
        Schema::dropIfExists('medications');
        Schema::dropIfExists('bed_types');
        Schema::dropIfExists('departments');
    }
};
