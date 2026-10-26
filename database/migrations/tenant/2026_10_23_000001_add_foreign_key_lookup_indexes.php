<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->index('invoice_id');
        });

        Schema::table('cash_payments', function (Blueprint $table) {
            $table->index('cash_shift_id');
            $table->index('invoice_id');
            $table->index('patient_id');
        });

        Schema::table('cash_shifts', function (Blueprint $table) {
            $table->index(['user_id', 'status']);
            $table->index('opened_at');
        });

        Schema::table('claims', function (Blueprint $table) {
            $table->index(['invoice_id', 'status']);
        });

        Schema::table('patient_insurances', function (Blueprint $table) {
            $table->index('patient_id');
            $table->index('payer_id');
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->index(['doctor_id', 'appointment_date']);
        });
    }

    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropIndex(['invoice_id']);
        });

        Schema::table('cash_payments', function (Blueprint $table) {
            $table->dropIndex(['cash_shift_id']);
            $table->dropIndex(['invoice_id']);
            $table->dropIndex(['patient_id']);
        });

        Schema::table('cash_shifts', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['opened_at']);
        });

        Schema::table('claims', function (Blueprint $table) {
            $table->dropIndex(['invoice_id', 'status']);
        });

        Schema::table('patient_insurances', function (Blueprint $table) {
            $table->dropIndex(['patient_id']);
            $table->dropIndex(['payer_id']);
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex(['doctor_id', 'appointment_date']);
        });
    }
};
