<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Link lab test definitions to services
        Schema::table('lab_test_definitions', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->after('is_active')
                ->constrained('services')->nullOnDelete();
        });

        // Link bed types to services
        Schema::table('bed_types', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->after('capacity')
                ->constrained('services')->nullOnDelete();
        });

        // Link medications to services
        Schema::table('medications', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->after('is_active')
                ->constrained('services')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('lab_test_definitions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('service_id');
        });
        Schema::table('bed_types', function (Blueprint $table) {
            $table->dropConstrainedForeignId('service_id');
        });
        Schema::table('medications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('service_id');
        });
    }
};
