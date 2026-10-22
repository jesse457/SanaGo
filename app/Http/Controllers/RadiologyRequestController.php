<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRadiologyRequestRequest;
use App\Models\Patient;
use App\Models\RadiologyRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RadiologyRequestController extends Controller
{
    public function index(Request $request): Response
    {
        $query = RadiologyRequest::with([
            'patient:id,first_name,last_name,patient_uid',
            'requestedBy:id,name',
            'result:id,request_id,status',
        ])->latest();

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();

            $query->where(function ($scoped) use ($search): void {
                $scoped->whereHas('patient', function ($patient) use ($search): void {
                    $patient->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('patient_uid', 'like', "%{$search}%");
                })
                    ->orWhere('imaging_type', 'like', "%{$search}%")
                    ->orWhere('body_part', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        $stats = RadiologyRequest::selectRaw("
            count(*) as total,
            count(case when status = 'pending' then 1 end) as pending,
            count(case when status = 'in_progress' then 1 end) as in_progress,
            count(case when status in ('completed', 'validated') then 1 end) as completed
        ")->first();

        return Inertia::render('Radiology/Requests/Index', [
            'requests' => $query->paginate(12)->withQueryString()
                ->through(fn (RadiologyRequest $request): array => $this->toRow($request)),
            'filters' => $request->only(['search', 'status']),
            'stats' => [
                'total' => (int) $stats->total,
                'pending' => (int) $stats->pending,
                'inProgress' => (int) $stats->in_progress,
                'completed' => (int) $stats->completed,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Radiology/Requests/Create', [
            'patients' => Patient::select('id', 'first_name', 'last_name', 'patient_uid', 'date_of_birth', 'gender')
                ->orderBy('last_name')
                ->get(),
        ]);
    }

    public function store(StoreRadiologyRequestRequest $request): RedirectResponse
    {
        RadiologyRequest::create([
            ...$request->validated(),
            'requested_by' => auth()->id(),
            'status' => 'pending',
        ]);

        return redirect()->route('radiology.requests.index')
            ->with('success', 'Radiology request created successfully.');
    }

    public function show(RadiologyRequest $request): Response
    {
        $request->load([
            'patient:id,first_name,last_name,patient_uid,date_of_birth,gender',
            'requestedBy:id,name',
            'result.performedBy:id,name',
            'result.validatedBy:id,name',
        ]);

        return Inertia::render('Radiology/Requests/Show', [
            'request' => array_merge($this->toRow($request), [
                'clinical_notes' => $request->clinical_notes,
                'patient' => $request->patient === null ? null : [
                    'id' => $request->patient->id,
                    'first_name' => $request->patient->first_name,
                    'last_name' => $request->patient->last_name,
                    'patient_uid' => $request->patient->patient_uid,
                    'date_of_birth' => $request->patient->date_of_birth?->toDateString(),
                    'gender' => $request->patient->gender?->value ?? $request->patient->gender,
                ],
                'result' => $request->result === null ? null : [
                    'id' => $request->result->id,
                    'request_id' => $request->result->request_id,
                    'status' => $request->result->status?->value,
                    'findings' => $request->result->findings,
                    'conclusion' => $request->result->conclusion,
                    'performed_by' => $request->result->performedBy?->name,
                    'validated_by' => $request->result->validatedBy?->name,
                    'validated_at' => $request->result->validated_at?->toIso8601String(),
                    'report_url' => $request->result->getFirstMediaUrl('report') ?: null,
                ],
            ]),
        ]);
    }

    public function destroy(RadiologyRequest $request): RedirectResponse
    {
        $request->delete();

        return redirect()->route('radiology.requests.index')
            ->with('success', 'Radiology request cancelled.');
    }

    public function print(RadiologyRequest $request): Response
    {
        return Inertia::render('Radiology/Requests/Print', [
            'request' => $request->load(['patient', 'requestedBy']),
        ]);
    }

    /**
     * The queue row the radiology screens share: patient, exam and the report
     * it may already carry. Both relations are nullable so a purged patient or
     * a deleted report degrades to a placeholder instead of a 500.
     *
     * @return array<string, mixed>
     */
    private function toRow(RadiologyRequest $request): array
    {
        $patient = $request->patient;
        $result = $request->result;

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
            ],
            'requested_by' => $request->requestedBy === null ? null : [
                'id' => $request->requestedBy->id,
                'name' => $request->requestedBy->name,
            ],
            'result' => $result === null ? null : [
                'id' => $result->id,
                'request_id' => $result->request_id,
                'status' => $result->status?->value,
                'updated_at' => $result->updated_at?->toDateString(),
            ],
        ];
    }
}
