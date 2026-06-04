<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demo_requests', function (Blueprint $table) {
            $table->string('pipeline_status')->default('new')->after('status');
            $table->foreignId('assigned_to')->nullable()->after('pipeline_status')->constrained('users')->nullOnDelete();
            $table->date('follow_up_date')->nullable()->after('assigned_to');
        });
    }

    public function down(): void
    {
        Schema::table('demo_requests', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropColumn(['pipeline_status', 'assigned_to', 'follow_up_date']);
        });
    }
};
