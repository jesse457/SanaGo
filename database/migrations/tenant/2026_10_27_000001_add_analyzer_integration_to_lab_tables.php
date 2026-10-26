<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lab analyzer integration.
 *
 * Cameroonian hospital labs are dominated by bench analyzers that speak ASTM
 * E1381/E1394 over a serial (USB) port — Mindray BC-3000/BC-5150, Sysmex
 * KX-21/XP-100, Biosystems A15/BTS-330, Genrui KT-6300, URIT, Rayto — plus a
 * smaller set that can push HL7 v2 ORU^R01 over TCP, and almost all of which
 * can export a CSV/TSV run report to USB.
 *
 * Three things are needed to accept those results:
 *
 * 1. `lab_requests.accession_number` — the sample id printed on the analyzer
 *    worklist and on the tube. It is the only reliable join key between a
 *    machine run and the request a doctor ordered.
 * 2. `lab_test_definitions.analyzer_code` — the short code the instrument
 *    uses ("GLU", "HGB", "CBC"), so incoming analytes can be tied to the
 *    catalogue.
 * 3. Provenance on `lab_results` plus an inbox of every raw frame, so a
 *    disputed value can always be traced back to the bytes the machine sent.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lab_requests', function (Blueprint $table) {
            $table->string('accession_number', 40)
                ->nullable()
                ->unique()
                ->after('id');
        });

        Schema::table('lab_test_definitions', function (Blueprint $table) {
            $table->string('analyzer_code', 40)
                ->nullable()
                ->index()
                ->after('code');
        });

        Schema::table('lab_results', function (Blueprint $table) {
            // manual | analyzer — how the values got here.
            $table->string('source', 20)->default('manual')->index();
            $table->string('analyzer_name', 120)->nullable();
            $table->string('analyzer_sample_id', 60)->nullable()->index();
            $table->json('analyzer_analytes')->nullable();
        });

        Schema::create('lab_analyzer_messages', function (Blueprint $table) {
            $table->id();
            // sha256 of the raw frame: a re-sent run is stored once.
            $table->string('message_hash', 64)->unique();
            $table->string('format', 20);
            $table->string('analyzer_name', 120)->nullable();
            $table->string('analyzer_serial', 80)->nullable();
            $table->string('sample_id', 60)->nullable()->index();
            // received | imported | unmatched | rejected
            $table->string('status', 20)->default('received')->index();
            $table->longText('raw_payload');
            $table->json('parsed')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('lab_request_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('lab_result_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('imported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('received_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_analyzer_messages');

        Schema::table('lab_results', function (Blueprint $table) {
            $table->dropColumn([
                'source',
                'analyzer_name',
                'analyzer_sample_id',
                'analyzer_analytes',
            ]);
        });

        Schema::table('lab_test_definitions', function (Blueprint $table) {
            $table->dropColumn('analyzer_code');
        });

        Schema::table('lab_requests', function (Blueprint $table) {
            $table->dropUnique(['accession_number']);
            $table->dropColumn('accession_number');
        });
    }
};
