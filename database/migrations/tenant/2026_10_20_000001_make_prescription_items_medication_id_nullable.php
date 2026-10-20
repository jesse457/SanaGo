<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Free-text / external drugs are prescribed without a catalogue entry, so
 * `prescription_items.medication_id` must be optional. It was NOT NULL, which
 * made every external-drug line in MedicalRecordService fail the insert.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prescription_items', function (Blueprint $table) {
            $table->foreignId('medication_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('prescription_items', function (Blueprint $table) {
            $table->foreignId('medication_id')->nullable(false)->change();
        });
    }
};
