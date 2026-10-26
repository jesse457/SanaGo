<?php

namespace App\Http\Controllers;

use App\Http\Requests\IngestAnalyzerMessageRequest;
use App\Models\LabAnalyzerMessage;
use App\Services\Laboratory\Analyzer\AnalyzerIngestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Lab analyzer endpoint.
 *
 * `ingest()` is what hospital hardware talks to. In Cameroon the analyzer is
 * almost always a bench instrument on a serial port (Mindray BC-3000/BC-5150,
 * Sysmex KX-21/XP-100, Biosystems A15, Genrui KT-6300, URIT, Rayto), so the
 * usual deployment is a tiny bridge on the analyzer's PC that relays each
 * ASTM E1394 frame here as text/plain; instruments with an Ethernet/HL7 option
 * post an ORU^R01 message directly. Both are accepted, plus CSV run exports.
 *
 *   curl -X POST https://hospital.test/lab/analyzer/ingest \
 *     -H "Authorization: Bearer $LAB_ANALYZER_TOKEN" \
 *     -H "Content-Type: text/plain" --data-binary @run.astm
 *
 * `preview()` backs the "read from analyzer" panel on the result entry screen:
 * it parses without writing, so the tech sees the numbers first.
 */
class LabAnalyzerController extends Controller
{
    public function __construct(private readonly AnalyzerIngestService $ingest) {}

    /**
     * Machine-facing: accept a frame and file it.
     */
    public function ingest(IngestAnalyzerMessageRequest $request): JsonResponse
    {
        if (! $this->machineAuthorised($request)) {
            return response()->json([
                'message' => 'This analyzer is not authorised. Check LAB_ANALYZER_TOKEN.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $message = $this->ingest->ingest(
            $request->frame(),
            $request->frameFormat(),
            $request->user(),
        );

        return response()->json([
            'status' => $message->status,
            'message_id' => $message->id,
            'format' => $message->format,
            'analyzer' => $message->analyzer_name,
            'sample_id' => $message->sample_id,
            'lab_request_id' => $message->lab_request_id,
            'lab_result_id' => $message->lab_result_id,
            'analytes' => count($message->parsed['results'] ?? []),
            'notes' => $message->notes,
        ], $message->status === LabAnalyzerMessage::STATUS_REJECTED
            ? Response::HTTP_UNPROCESSABLE_ENTITY
            : Response::HTTP_CREATED);
    }

    /**
     * Screen-facing: parse a pasted/uploaded frame and resolve it, no writes.
     */
    public function preview(IngestAnalyzerMessageRequest $request): JsonResponse
    {
        // Screen-only: a preview edits nothing, but it must still be someone
        // who is allowed to file this patient's result.
        abort_unless($request->user()?->can('create-lab-results'), 403);

        return response()->json(
            $this->ingest->preview($request->frame(), $request->frameFormat()),
        );
    }

    /**
     * A machine must present the configured token; a signed-in tech who holds
     * the permission may also push (useful when testing a bridge from the app).
     */
    private function machineAuthorised(Request $request): bool
    {
        $token = config('lab-analyzer.token');

        if ($request->user() !== null) {
            return true;
        }

        if (! is_string($token) || $token === '') {
            return false;
        }

        $provided = (string) $request->bearerToken();

        return $provided !== '' && hash_equals($token, $provided);
    }
}
