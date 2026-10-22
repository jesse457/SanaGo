<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRadiologyResultRequest;
use App\Http\Requests\UpdateRadiologyResultRequest;
use App\Models\RadiologyRequest;
use App\Models\RadiologyResult;
use App\Services\Radiology\RadiologyResultService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RadiologyResultController extends Controller
{
    public function index(Request $request): Response
    {
        $query = RadiologyResult::with([
            'request:id,patient_id,imaging_type,body_part,status,created_at',
            'request.patient:id,first_name,last_name,patient_uid',
            'performedBy:id,name',
            'validatedBy:id,name',
        ])->latest('updated_at');

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();

            $query->where(function ($scoped) use ($search): void {
                $scoped->whereHas('request.patient', function ($patient) use ($search): void {
                    $patient->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('patient_uid', 'like', "%{$search}%");
                })
                    ->orWhereHas('request', function ($imaging) use ($search): void {
                        $imaging->where('imaging_type', 'like', "%{$search}%")
                            ->orWhere('body_part', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        $stats = RadiologyResult::selectRaw("
            count(*) as total,
            count(case when status in ('completed', 'validated') then 1 end) as signed,
            count(case when status = 'validated' then 1 end) as validated,
            count(case when status = 'in_progress' then 1 end) as in_progress
        ")->first();

        return Inertia::render('Radiology/Results/Index', [
            'results' => $query->paginate(10)->withQueryString()
                ->through(fn (RadiologyResult $result): array => $this->toRow($result)),
            'filters' => $request->only(['search', 'status']),
            'stats' => [
                'total' => (int) $stats->total,
                'signed' => (int) $stats->signed,
                'validated' => (int) $stats->validated,
                'inProgress' => (int) $stats->in_progress,
            ],
        ]);
    }

    /**
     * Reports are always recorded against a request. Opening the form without
     * one sends the radiographer back to the queue, and a request that already
     * carries a report opens that report instead of a duplicate form.
     */
    public function create(Request $request): Response|RedirectResponse
    {
        $requestId = $request->integer('request_id');

        if ($requestId === 0) {
            return redirect()
                ->route('radiology.requests.index')
                ->with('error', 'Open an imaging request and choose "process result" to record its report.');
        }

        $radiologyRequest = RadiologyRequest::with(['patient', 'requestedBy', 'result'])
            ->findOrFail($requestId);

        if ($radiologyRequest->result !== null) {
            return redirect()
                ->route('radiology.results.edit', $radiologyRequest->result)
                ->with('info', 'This request already has a report. Edit it instead of recording a second one.');
        }

        return Inertia::render('Radiology/Results/Enter', [
            'request' => $this->requestPayload($radiologyRequest),
            'result' => null,
            'isEdit' => false,
        ]);
    }

    public function store(StoreRadiologyResultRequest $request, RadiologyResultService $service): RedirectResponse
    {
        $service->createResult($request->validated());

        return redirect()->route('radiology.results.index')
            ->with('success', 'Radiology report saved.');
    }

    public function edit(RadiologyResult $result): Response
    {
        $result->load([
            'request.patient',
            'request.requestedBy',
            'performedBy:id,name',
            'validatedBy:id,name',
        ]);

        return Inertia::render('Radiology/Results/Enter', [
            'request' => $this->requestPayload($result->request),
            'result' => $this->toRow($result),
            'isEdit' => true,
        ]);
    }

    public function update(
        UpdateRadiologyResultRequest $request,
        RadiologyResult $result,
        RadiologyResultService $service,
    ): RedirectResponse {
        $service->updateResult($result, $request->validated());

        return redirect()->route('radiology.results.index')
            ->with('success', 'Radiology report updated.');
    }

    public function validate(RadiologyResult $result, RadiologyResultService $service): RedirectResponse
    {
        $service->validateResult($result);

        return back()->with('success', 'Radiology result validated successfully.');
    }

    /**
     * The exam the report belongs to, as the report screen renders it.
     *
     * @return array<string, mixed>
     */
    private function requestPayload(?RadiologyRequest $request): ?array
    {
        if ($request === null) {
            return null;
        }

        $patient = $request->patient;

        return [
            'id' => $request->id,
            'status' => $request->status?->value,
            'request_date' => $request->created_at?->toDateString(),
            'imaging_type' => $request->imaging_type,
            'body_part' => $request->body_part,
            'clinical_notes' => $request->clinical_notes,
            'patient' => $patient === null ? null : [
                'id' => $patient->id,
                'first_name' => $patient->first_name,
                'last_name' => $patient->last_name,
                'patient_uid' => $patient->patient_uid,
                'date_of_birth' => $patient->date_of_birth?->toDateString(),
                'gender' => $patient->gender?->value ?? $patient->gender,
            ],
            'requested_by' => $request->requestedBy === null ? null : [
                'id' => $request->requestedBy->id,
                'name' => $request->requestedBy->name,
            ],
        ];
    }

    /**
     * The archive row the results screens share.
     *
     * @return array<string, mixed>
     */
    private function toRow(RadiologyResult $result): array
    {
        $request = $result->request;
        $patient = $request?->patient;

        return [
            'id' => $result->id,
            'request_id' => $result->request_id,
            'status' => $result->status?->value,
            'report_date' => ($result->validated_at ?? $result->updated_at)?->toDateString(),
            'findings' => $result->findings,
            'conclusion' => $result->conclusion,
            'imaging_type' => $request?->imaging_type,
            'body_part' => $request?->body_part,
            'request_status' => $request?->status?->value,
            'patient' => $patient === null ? null : [
                'id' => $patient->id,
                'first_name' => $patient->first_name,
                'last_name' => $patient->last_name,
                'patient_uid' => $patient->patient_uid,
            ],
            'performed_by' => $result->performedBy === null ? null : [
                'id' => $result->performedBy->id,
                'name' => $result->performedBy->name,
            ],
            'validated_by' => $result->validatedBy === null ? null : [
                'id' => $result->validatedBy->id,
                'name' => $result->validatedBy->name,
            ],
        ];
    }
}
