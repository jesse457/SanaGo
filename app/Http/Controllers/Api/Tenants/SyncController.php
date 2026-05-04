<?php

namespace App\Http\Controllers\Api\Tenants;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Patient;
use App\Models\Medication;
use App\Models\LabTestDefinition;
use App\Models\Bed;
use App\Models\Ward;
use App\Models\Department;
use App\Models\UserShift;
use App\Models\Appointment;
use App\Models\Admission;
use App\Models\LabRequest;
use App\Models\Prescription;
use App\Models\MedicalRecord;
use App\Models\Supply;
use App\Models\SupplyUsage;
use App\Models\Dispensation;
use App\Models\Vital;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * RxDB Sync Controller - Dexie Optimized
 *
 * Optimized for RxDB with Dexie storage:
 * - Flat documents (no nested objects for Dexie indexing)
 * - String IDs only (Dexie requirement)
 * - Minimal payload size
 * - Proper revision handling for conflict resolution
 */
class SyncController extends Controller
{
    use HttpResponses;

    protected const BATCH_SIZE = 100;
    protected const MAX_PUSH_SIZE = 50;

    /**
     * Collections that are workflow (read-only via sync)
     */
    protected const WORKFLOW_COLLECTIONS = [
        'appointments',
        'admissions',
        'lab_requests',
        'prescriptions',
        'medical_records',
        'vitals',
        'dispensations',
        'supply_usages'
    ];

    /**
     * ============================================
     * ADMIN SYNC
     * ============================================
     */

    public function adminPull(Request $request): JsonResponse
    {
        $checkpoint = $this->parseCheckpoint($request->input('checkpoint'));
        $context = $request->input('context', 'dashboard');

        $collections = match ($context) {
            'staff' => ['users', 'shifts', 'departments'],
            'settings' => ['departments', 'wards', 'bed_types', 'beds', 'supplies'],
            default => ['users', 'shifts', 'departments', 'dashboard_stats', 'bed_occupancy']
        };

        return $this->executePull($collections, $checkpoint, null);
    }

    public function adminPush(Request $request): JsonResponse
    {
        return $this->handlePush($request, ['users', 'shifts', 'departments', 'wards', 'beds', 'supplies'], 'admin');
    }

    /**
     * ============================================
     * RECEPTIONIST SYNC
     * ============================================
     */

    public function receptionistPull(Request $request): JsonResponse
    {
        $checkpoint = $this->parseCheckpoint($request->input('checkpoint'));
        $context = $request->input('context', 'dashboard');

        $collections = match ($context) {
            'patient_search' => ['patients'],
            'bed_management' => ['beds', 'wards', 'departments', 'admissions_active'],
            'appointment_booking' => ['patients', 'doctors', 'appointments_today'],
            default => ['dashboard_stats', 'patients_recent', 'appointments_today', 'admissions_pending', 'beds']
        };

        return $this->executePull($collections, $checkpoint, null);
    }

    public function receptionistPush(Request $request): JsonResponse
    {
        return $this->handlePush($request, ['patients'], 'receptionist');
    }

    /**
     * ============================================
     * DOCTOR SYNC
     * ============================================
     */

    public function doctorPull(Request $request): JsonResponse
    {
        $doctorId = Auth::id();
        $checkpoint = $this->parseCheckpoint($request->input('checkpoint'));
        $context = $request->input('context', 'dashboard');

        $collections = match ($context) {
            'consultation' => ['medications', 'lab_definitions', 'lab_technicians', 'my_patients_list', 'medical_records_finalized'],
            'my_patients' => ['my_patients_list', 'medical_records_finalized'],
            'schedule' => ['my_appointments_today', 'my_appointments_week'],
            default => ['dashboard_stats', 'my_appointments_today', 'my_patients_list', 'medications', 'lab_definitions', 'departments']
        };

        return $this->executePull($collections, $checkpoint, $doctorId);
    }

    public function doctorPush(Request $request): JsonResponse
    {
        return $this->readOnlyResponse();
    }

    /**
     * ============================================
     * PHARMACIST SYNC
     * ============================================
     */

    public function pharmacistPull(Request $request): JsonResponse
    {
        $checkpoint = $this->parseCheckpoint($request->input('checkpoint'));
        $context = $request->input('context', 'dashboard');

        $collections = match ($context) {
            'dispensing' => ['pending_prescriptions', 'medications', 'patients'],
            'inventory' => ['medications', 'low_stock_alerts', 'supplies'],
            default => ['dashboard_stats', 'medications', 'pending_prescriptions', 'supplies']
        };

        return $this->executePull($collections, $checkpoint, null);
    }

    public function pharmacistPush(Request $request): JsonResponse
    {
        return $this->handlePush($request, ['medications'], 'pharmacist');
    }

    /**
     * ============================================
     * LAB TECHNICIAN SYNC
     * ============================================
     */

    public function labTechnicianPull(Request $request): JsonResponse
    {
        $checkpoint = $this->parseCheckpoint($request->input('checkpoint'));
        $context = $request->input('context', 'dashboard');

        $collections = match ($context) {
            'processing' => ['my_assigned_requests', 'test_definitions', 'lab_equipment'],
            'definitions' => ['test_definitions'],
            default => ['dashboard_stats', 'pending_requests', 'test_definitions', 'my_assigned_requests']
        };

        return $this->executePull($collections, $checkpoint, null);
    }

    public function labTechnicianPush(Request $request): JsonResponse
    {
        return $this->handlePush($request, ['lab_test_definitions'], 'lab_technician');
    }

    /**
     * ============================================
     * NURSE SYNC
     * ============================================
     */

    public function nursePull(Request $request): JsonResponse
    {
        $checkpoint = $this->parseCheckpoint($request->input('checkpoint'));
        $context = $request->input('context', 'dashboard');

        $collections = match ($context) {
            'rounds' => ['admitted_patients', 'vitals_recent', 'supplies'],
            'bed_map' => ['beds_occupied', 'wards', 'departments'],
            'supply_usage' => ['supplies', 'supply_usages_recent'],
            default => ['dashboard_stats', 'admitted_patients', 'beds_occupied', 'supplies']
        };

        return $this->executePull($collections, $checkpoint, null);
    }

    public function nursePush(Request $request): JsonResponse
    {
        return $this->readOnlyResponse();
    }

    /**
     * ============================================
     * PULL METHODS - REFERENCE DATA
     * ============================================
     */

    protected function pullDepartments(array $checkpoint, int $limit, ?int $userId): array
    {
        $docs = Department::where(function ($q) use ($checkpoint) {
            $q->where('updated_at', '>', $checkpoint['updated_at'])
                ->orWhere(function ($sub) use ($checkpoint) {
                    $sub->where('updated_at', '=', $checkpoint['updated_at'])
                        ->where('id', '>', $checkpoint['id']);
                });
        })
            ->select('id', 'name', 'description', 'updated_at')
            ->orderBy('name')
            ->limit($limit + 1)
            ->get();

        return $this->formatResponse($docs, $limit, fn($d) => [
            'id' => (string) $d->id,
            'name' => $d->name,
            'description' => $d->description,
        ]);
    }

    protected function pullSupplies(array $checkpoint, int $limit, ?int $userId): array
    {
        $docs = Supply::where(function ($q) use ($checkpoint) {
            $q->where('updated_at', '>', $checkpoint['updated_at'])
                ->orWhere(function ($sub) use ($checkpoint) {
                    $sub->where('updated_at', '=', $checkpoint['updated_at'])
                        ->where('id', '>', $checkpoint['id']);
                });
        })
            ->select('id', 'name', 'unit_of_measure', 'current_stock', 'min_stock_level', 'updated_at')
            ->orderBy('name')
            ->limit($limit + 1)
            ->get();

        return $this->formatResponse($docs, $limit, fn($s) => [
            'id' => (string) $s->id,
            'name' => $s->name,
            'unit' => $s->unit_of_measure,
            'stock' => (int) $s->current_stock,
            'min_stock' => (int) $s->min_stock_level,
            'low_stock' => $s->current_stock <= $s->min_stock_level,
        ]);
    }

    protected function pullUsers(array $checkpoint, int $limit, ?int $userId): array
    {
        $docs = User::where('is_active', true)
            ->where(function ($q) use ($checkpoint) {
                $q->where('updated_at', '>', $checkpoint['updated_at'])
                    ->orWhere(function ($sub) use ($checkpoint) {
                        $sub->where('updated_at', '=', $checkpoint['updated_at'])
                            ->where('id', '>', $checkpoint['id']);
                    });
            })
            ->select('id', 'name', 'email', 'role', 'department_id', 'phone_number', 'profile_picture', 'updated_at')
            ->orderBy('name')
            ->limit($limit + 1)
            ->get();

        return $this->formatResponse($docs, $limit, fn($u) => [
            'id' => (string) $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'role' => $u->role,
            'department_id' => $u->department_id ? (string) $u->department_id : null,
            'phone' => $u->phone_number,
            'avatar' => $u->profile_picture,
        ]);
    }

    protected function pullPatients(array $checkpoint, int $limit, ?int $userId): array
    {
        $docs = Patient::where(function ($q) use ($checkpoint) {
            $q->where('updated_at', '>', $checkpoint['updated_at'])
                ->orWhere(function ($sub) use ($checkpoint) {
                    $sub->where('updated_at', '=', $checkpoint['updated_at'])
                        ->where('id', '>', $checkpoint['id']);
                });
        })
            ->select('id', 'patient_uid', 'first_name', 'last_name', 'phone', 'email', 'gender', 'dob', 'updated_at')
            ->orderBy('last_name')
            ->limit($limit + 1)
            ->get();

        return $this->formatResponse($docs, $limit, fn($p) => [
            'id' => (string) $p->id,
            'uid' => $p->patient_uid,
            'first_name' => $p->first_name,
            'last_name' => $p->last_name,
            'phone' => $p->phone,
            'email' => $p->email,
            'gender' => $p->gender,
            'dob' => $p->dob?->toDateString(),
        ]);
    }

    protected function pullMedications(array $checkpoint, int $limit, ?int $userId): array
    {
        $docs = Medication::where(function ($q) use ($checkpoint) {
            $q->where('updated_at', '>', $checkpoint['updated_at'])
                ->orWhere(function ($sub) use ($checkpoint) {
                    $sub->where('updated_at', '=', $checkpoint['updated_at'])
                        ->where('id', '>', $checkpoint['id']);
                });
        })
            ->select('id', 'name', 'generic_name', 'description', 'stock_quantity', 'min_stock_level', 'unit_price_purchase', 'updated_at')
            ->orderBy('name')
            ->limit($limit + 1)
            ->get();

        return $this->formatResponse($docs, $limit, fn($m) => [
            'id' => (string) $m->id,
            'name' => $m->name,
            'generic_name' => $m->generic_name,
            'description' => $m->description,
            'stock' => (int) $m->stock_quantity,
            'min_stock' => (int) $m->min_stock_level,
            'price' => (float) $m->unit_price_purchase,
            'in_stock' => $m->stock_quantity > 0,
        ]);
    }

    protected function pullLabDefinitions(array $checkpoint, int $limit, ?int $userId): array
    {
        $docs = LabTestDefinition::where(function ($q) use ($checkpoint) {
            $q->where('updated_at', '>', $checkpoint['updated_at'])
                ->orWhere(function ($sub) use ($checkpoint) {
                    $sub->where('updated_at', '=', $checkpoint['updated_at'])
                        ->where('id', '>', $checkpoint['id']);
                });
        })
            ->select('id', 'test_name', 'test_code', 'description', 'price', 'units', 'normal_range', 'updated_at')
            ->orderBy('test_name')
            ->limit($limit + 1)
            ->get();

        return $this->formatResponse($docs, $limit, fn($t) => [
            'id' => (string) $t->id,
            'name' => $t->test_name,
            'code' => $t->test_code,
            'description' => $t->description,
            'price' => (float) $t->price,
            'units' => $t->units,
            'normal_range' => $t->normal_range,
        ]);
    }

    protected function pullBeds(array $checkpoint, int $limit, ?int $userId): array
    {
        $docs = Bed::with(['ward:id,name', 'bedType:id,name'])
            ->where(function ($q) use ($checkpoint) {
                $q->where('updated_at', '>', $checkpoint['updated_at'])
                    ->orWhere(function ($sub) use ($checkpoint) {
                        $sub->where('updated_at', '=', $checkpoint['updated_at'])
                            ->where('id', '>', $checkpoint['id']);
                    });
            })
            ->select('id', 'bed_number', 'ward_id', 'bed_type_id', 'is_occupied', 'updated_at')
            ->orderBy('bed_number')
            ->limit($limit + 1)
            ->get();

        return $this->formatResponse($docs, $limit, fn($b) => [
            'id' => (string) $b->id,
            'number' => $b->bed_number,
            'ward_id' => (string) $b->ward_id,
            'ward_name' => $b->ward->name ?? '',
            'type_id' => $b->bed_type_id ? (string) $b->bed_type_id : null,
            'type_name' => $b->bedType->name ?? '',
            'occupied' => (bool) $b->is_occupied,
        ]);
    }

    protected function pullWards(array $checkpoint, int $limit, ?int $userId): array
    {
        $docs = Ward::with('department:id,name')
            ->where(function ($q) use ($checkpoint) {
                $q->where('updated_at', '>', $checkpoint['updated_at'])
                    ->orWhere(function ($sub) use ($checkpoint) {
                        $sub->where('updated_at', '=', $checkpoint['updated_at'])
                            ->where('id', '>', $checkpoint['id']);
                    });
            })
            ->select('id', 'name', 'ward_number', 'department_id', 'description', 'updated_at')
            ->orderBy('name')
            ->limit($limit + 1)
            ->get();

        return $this->formatResponse($docs, $limit, fn($w) => [
            'id' => (string) $w->id,
            'name' => $w->name,
            'number' => $w->ward_number,
            'department_id' => (string) $w->department_id,
            'department_name' => $w->department->name ?? '',
            'description' => $w->description,
        ]);
    }

    protected function pullShifts(array $checkpoint, int $limit, ?int $userId): array
    {
        $docs = UserShift::whereDate('shift_date', '>=', now()->subDays(7))
            ->whereDate('shift_date', '<=', now()->addDays(14))
            ->where(function ($q) use ($checkpoint) {
                $q->where('updated_at', '>', $checkpoint['updated_at'])
                    ->orWhere(function ($sub) use ($checkpoint) {
                        $sub->where('updated_at', '=', $checkpoint['updated_at'])
                            ->where('id', '>', $checkpoint['id']);
                    });
            })
            ->select('id', 'shift_type', 'shift_date', 'start_time', 'end_time', 'updated_at')
            ->orderBy('shift_date')
            ->limit($limit + 1)
            ->get();

        return $this->formatResponse($docs, $limit, fn($s) => [
            'id' => (string) $s->id,
            'type' => $s->shift_type,
            'date' => $s->shift_date->toDateString(),
            'start' => $s->start_time,
            'end' => $s->end_time,
        ]);
    }

    /**
     * ============================================
     * PULL METHODS - WORKFLOW DATA (READ-ONLY)
     * ============================================
     */

    protected function pullAppointmentsToday(array $checkpoint, int $limit, ?int $userId): array
    {
        $docs = Appointment::whereDate('appointment_date', today())
            ->with(['patient:id,first_name,last_name,patient_uid', 'doctor:id,name'])
            ->where(function ($q) use ($checkpoint) {
                $q->where('updated_at', '>', $checkpoint['updated_at'])
                    ->orWhere(function ($sub) use ($checkpoint) {
                        $sub->where('updated_at', '=', $checkpoint['updated_at'])
                            ->where('id', '>', $checkpoint['id']);
                    });
            })
            ->orderBy('appointment_time')
            ->limit($limit + 1)
            ->get();

        return $this->formatResponse($docs, $limit, fn($a) => [
            'id' => (string) $a->id,
            'patient_id' => (string) $a->patient_id,
            'patient_name' => $a->patient ? $a->patient->first_name . ' ' . $a->patient->last_name : '',
            'patient_uid' => $a->patient->patient_uid ?? '',
            'doctor_id' => (string) $a->doctor_id,
            'doctor_name' => $a->doctor->name ?? '',
            'date' => $a->appointment_date->toDateString(),
            'time' => $a->appointment_time,
            'status' => $a->status,
            'reason' => $a->reason_for_visit,
            'queue_position' => (int) $a->queue_position,
        ], true);
    }

    protected function pullAdmissionsActive(array $checkpoint, int $limit, ?int $userId): array
    {
        $docs = Admission::where('status', 'admitted')
            ->with(['patient:id,first_name,last_name', 'doctor:id,name', 'bed:id,bed_number'])
            ->where(function ($q) use ($checkpoint) {
                $q->where('updated_at', '>', $checkpoint['updated_at'])
                    ->orWhere(function ($sub) use ($checkpoint) {
                        $sub->where('updated_at', '=', $checkpoint['updated_at'])
                            ->where('id', '>', $checkpoint['id']);
                    });
            })
            ->orderBy('admission_date')
            ->limit($limit + 1)
            ->get();

        return $this->formatResponse($docs, $limit, fn($a) => [
            'id' => (string) $a->id,
            'patient_id' => (string) $a->patient_id,
            'patient_name' => $a->patient ? $a->patient->first_name . ' ' . $a->patient->last_name : '',
            'doctor_id' => (string) $a->doctor_id,
            'doctor_name' => $a->doctor->name ?? '',
            'bed_id' => $a->bed_id ? (string) $a->bed_id : null,
            'bed_number' => $a->bed->bed_number ?? '',
            'admission_date' => $a->admission_date?->toDateTimeString(),
            'status' => $a->status,
        ], true);
    }

    protected function pullAdmissionsPending(array $checkpoint, int $limit, ?int $userId): array
    {
        $docs = Admission::where('status', 'pending')
            ->with(['patient:id,first_name,last_name', 'doctor:id,name'])
            ->where(function ($q) use ($checkpoint) {
                $q->where('updated_at', '>', $checkpoint['updated_at']);
            })
            ->orderBy('created_at')
            ->limit($limit + 1)
            ->get();

        return $this->formatResponse($docs, $limit, fn($a) => [
            'id' => (string) $a->id,
            'patient_id' => (string) $a->patient_id,
            'patient_name' => $a->patient ? $a->patient->first_name . ' ' . $a->patient->last_name : '',
            'doctor_id' => (string) $a->doctor_id,
            'doctor_name' => $a->doctor->name ?? '',
            'requested_at' => $a->created_at->toDateTimeString(),
        ], true);
    }

    protected function pullMedicalRecordsFinalized(array $checkpoint, int $limit, ?int $doctorId): array
    {
        if (!$doctorId) return ['documents' => [], 'hasMore' => false, 'lastDoc' => null];

        $docs = MedicalRecord::where('doctor_id', $doctorId)
            ->where('finalized', true)
            ->with('patient:id,first_name,last_name')
            ->where(function ($q) use ($checkpoint) {
                $q->where('updated_at', '>', $checkpoint['updated_at'])
                    ->orWhere(function ($sub) use ($checkpoint) {
                        $sub->where('updated_at', '=', $checkpoint['updated_at'])
                            ->where('id', '>', $checkpoint['id']);
                    });
            })
            ->orderBy('updated_at', 'desc')
            ->limit($limit + 1)
            ->get();

        return $this->formatResponse($docs, $limit, fn($r) => [
            'id' => (string) $r->id,
            'patient_id' => (string) $r->patient_id,
            'patient_name' => $r->patient ? $r->patient->first_name . ' ' . $r->patient->last_name : '',
            'complaint' => $r->complaint,
            'diagnosis' => $r->diagnosis_text,
            'treatment' => $r->treatment_plan,
            'finalized' => true,
            'created_at' => $r->created_at->toDateTimeString(),
        ], true);
    }

    protected function pullPendingPrescriptions(array $checkpoint, int $limit, ?int $userId): array
    {
        $docs = Prescription::whereIn('status', ['pending', 'partially_dispensed'])
            ->with(['patient:id,first_name,last_name', 'doctor:id,name'])
            ->with(['items' => fn($q) => $q->with('medication:id,name')])
            ->where(function ($q) use ($checkpoint) {
                $q->where('updated_at', '>', $checkpoint['updated_at'])
                    ->orWhere(function ($sub) use ($checkpoint) {
                        $sub->where('updated_at', '=', $checkpoint['updated_at'])
                            ->where('id', '>', $checkpoint['id']);
                    });
            })
            ->orderBy('prescription_date')
            ->limit($limit + 1)
            ->get();

        return $this->formatResponse($docs, $limit, fn($p) => [
            'id' => (string) $p->id,
            'patient_id' => (string) $p->patient_id,
            'patient_name' => $p->patient ? $p->patient->first_name . ' ' . $p->patient->last_name : '',
            'doctor_name' => $p->doctor->name ?? '',
            'date' => $p->prescription_date?->toDateTimeString(),
            'status' => $p->status,
            'items' => $p->items->map(fn($i) => [
                'id' => (string) $i->id,
                'medication' => $i->medication->name ?? '',
                'dosage' => $i->dosage,
                'frequency' => $i->frequency,
                'duration' => $i->duration,
                'prescribed' => (int) $i->quantity_prescribed,
                'dispensed' => (int) ($i->dispensed_quantity ?? 0),
            ])->toArray(),
        ], true);
    }

    protected function pullPendingRequests(array $checkpoint, int $limit, ?int $userId): array
    {
        $docs = LabRequest::where('status', 'pending')
            ->with(['patient:id,first_name,last_name,patient_uid', 'testDefinition:id,test_name', 'doctor:id,name'])
            ->where(function ($q) use ($checkpoint) {
                $q->where('updated_at', '>', $checkpoint['updated_at'])
                    ->orWhere(function ($sub) use ($checkpoint) {
                        $sub->where('updated_at', '=', $checkpoint['updated_at'])
                            ->where('id', '>', $checkpoint['id']);
                    });
            })
            ->orderBy('urgency_level', 'desc')
            ->orderBy('request_date')
            ->limit($limit + 1)
            ->get();

        return $this->formatResponse($docs, $limit, fn($r) => [
            'id' => (string) $r->id,
            'patient_id' => (string) $r->patient_id,
            'patient_name' => $r->patient ? $r->patient->first_name . ' ' . $r->patient->last_name : '',
            'patient_uid' => $r->patient->patient_uid ?? '',
            'test_name' => $r->testDefinition->test_name ?? '',
            'urgency' => $r->urgency_level,
            'doctor_name' => $r->doctor->name ?? '',
            'requested_at' => $r->request_date?->toDateTimeString(),
        ], true);
    }

    protected function pullAdmittedPatients(array $checkpoint, int $limit, ?int $userId): array
    {
        $docs = Admission::where('status', 'admitted')
            ->with(['patient:id,first_name,last_name,dob,gender,blood_type,allergies', 'bed:id,bed_number,ward_id', 'bed.ward:id,name', 'doctor:id,name'])
            ->where(function ($q) use ($checkpoint) {
                $q->where('updated_at', '>', $checkpoint['updated_at'])
                    ->orWhere(function ($sub) use ($checkpoint) {
                        $sub->where('updated_at', '=', $checkpoint['updated_at'])
                            ->where('id', '>', $checkpoint['id']);
                    });
            })
            ->orderBy('admission_date')
            ->limit($limit + 1)
            ->get();

        return $this->formatResponse($docs, $limit, fn($a) => [
            'id' => (string) $a->id,
            'patient_id' => (string) $a->patient_id,
            'patient_name' => $a->patient ? $a->patient->first_name . ' ' . $a->patient->last_name : '',
            'age' => $a->patient && $a->patient->dob ? now()->diffInYears($a->patient->dob) : null,
            'gender' => $a->patient->gender ?? '',
            'blood_type' => $a->patient->blood_type ?? '',
            'allergies' => $a->patient->allergies ?? '',
            'bed_id' => $a->bed_id ? (string) $a->bed_id : null,
            'bed_number' => $a->bed->bed_number ?? '',
            'ward' => $a->bed->ward->name ?? '',
            'doctor_name' => $a->doctor->name ?? '',
            'admitted_since' => $a->admission_date?->toDateTimeString(),
        ], true);
    }

    protected function pullSupplyUsagesRecent(array $checkpoint, int $limit, ?int $userId): array
    {
        $docs = SupplyUsage::whereDate('created_at', '>=', now()->subDays(7))
            ->with(['supply:id,name', 'patient:id,first_name,last_name', 'user:id,name'])
            ->where(function ($q) use ($checkpoint) {
                $q->where('created_at', '>', $checkpoint['updated_at'])
                    ->orWhere(function ($sub) use ($checkpoint) {
                        $sub->where('created_at', '=', $checkpoint['updated_at'])
                            ->where('id', '>', $checkpoint['id']);
                    });
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit + 1)
            ->get();

        return $this->formatResponse($docs, $limit, fn($u) => [
            'id' => (string) $u->id,
            'supply_id' => (string) $u->supply_id,
            'supply_name' => $u->supply->name ?? '',
            'patient_id' => (string) $u->patient_id,
            'patient_name' => $u->patient ? $u->patient->first_name . ' ' . $u->patient->last_name : '',
            'user_name' => $u->user->name ?? '',
            'quantity' => (int) $u->quantity_used,
            'used_at' => $u->usage_date?->toDateTimeString(),
        ], true);
    }

    /**
     * ============================================
     * ROLE-SPECIFIC PULLS
     * ============================================
     */

    protected function pullMyAppointmentsToday(array $checkpoint, int $limit, ?int $doctorId): array
    {
        if (!$doctorId) return ['documents' => [], 'hasMore' => false, 'lastDoc' => null];

        $docs = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', today())
            ->with('patient:id,first_name,last_name,patient_uid,phone')
            ->where(function ($q) use ($checkpoint) {
                $q->where('updated_at', '>', $checkpoint['updated_at'])
                    ->orWhere(function ($sub) use ($checkpoint) {
                        $sub->where('updated_at', '=', $checkpoint['updated_at'])
                            ->where('id', '>', $checkpoint['id']);
                    });
            })
            ->orderBy('appointment_time')
            ->limit($limit + 1)
            ->get();

        return $this->formatResponse($docs, $limit, fn($a) => [
            'id' => (string) $a->id,
            'patient_id' => (string) $a->patient_id,
            'patient_name' => $a->patient ? $a->patient->first_name . ' ' . $a->patient->last_name : '',
            'patient_uid' => $a->patient->patient_uid ?? '',
            'patient_phone' => $a->patient->phone ?? '',
            'time' => $a->appointment_time,
            'status' => $a->status,
            'reason' => $a->reason_for_visit,
            'queue_position' => (int) $a->queue_position,
        ], true);
    }

    protected function pullMyAppointmentsWeek(array $checkpoint, int $limit, ?int $doctorId): array
    {
        if (!$doctorId) return ['documents' => [], 'hasMore' => false, 'lastDoc' => null];

        $docs = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', '>=', today())
            ->whereDate('appointment_date', '<=', now()->addDays(7))
            ->with('patient:id,first_name,last_name')
            ->select('id', 'patient_id', 'appointment_date', 'appointment_time', 'status', 'updated_at')
            ->orderBy('appointment_date')
            ->limit($limit + 1)
            ->get();

        return $this->formatResponse($docs, $limit, fn($a) => [
            'id' => (string) $a->id,
            'date' => $a->appointment_date->toDateString(),
            'time' => $a->appointment_time,
            'patient_name' => $a->patient ? $a->patient->first_name . ' ' . $a->patient->last_name : '',
            'status' => $a->status,
        ], true);
    }

    protected function pullMyPatientsList(array $checkpoint, int $limit, ?int $doctorId): array
    {
        if (!$doctorId) return ['documents' => [], 'hasMore' => false, 'lastDoc' => null];

        $patientIds = Appointment::where('doctor_id', $doctorId)->pluck('patient_id')
            ->merge(Admission::where('doctor_id', $doctorId)->pluck('patient_id'))
            ->unique();

        $docs = Patient::whereIn('id', $patientIds)
            ->where(function ($q) use ($checkpoint) {
                $q->where('updated_at', '>', $checkpoint['updated_at'])
                    ->orWhere(function ($sub) use ($checkpoint) {
                        $sub->where('updated_at', '=', $checkpoint['updated_at'])
                            ->where('id', '>', $checkpoint['id']);
                    });
            })

            ->orderBy('last_name')
            ->limit($limit + 1)
            ->get();

        return $this->formatResponse($docs, $limit, fn($p) => [
            'id' => (string) $p->id,
            'uid' => $p->patient_uid,
            'name' => $p->first_name . ' ' . $p->last_name,
            'phone' => $p->phone,
            'age' => $p->dob ? now()->diffInYears($p->dob) : null,
            'gender' => $p->gender,
        ]);
    }

    protected function pullMyAssignedRequests(array $checkpoint, int $limit, ?int $userId): array
    {
        if (!$userId) return ['documents' => [], 'hasMore' => false, 'lastDoc' => null];

        $docs = LabRequest::where('lab_tech_id', $userId)
            ->where('status', 'processing')
            ->with(['patient:id,first_name,last_name', 'testDefinition:id,test_name'])
            ->where(function ($q) use ($checkpoint) {
                $q->where('updated_at', '>', $checkpoint['updated_at']);
            })
            ->orderBy('request_date')
            ->limit($limit + 1)
            ->get();

        return $this->formatResponse($docs, $limit, fn($r) => [
            'id' => (string) $r->id,
            'patient_name' => $r->patient ? $r->patient->first_name . ' ' . $r->patient->last_name : '',
            'test_name' => $r->testDefinition->test_name ?? '',
            'started_at' => $r->updated_at->toDateTimeString(),
        ], true);
    }

    /**
     * ============================================
     * DASHBOARD & UTILITY
     * ============================================
     */

    protected function pullDashboardStats(array $checkpoint, int $limit, ?int $userId): array
    {
        $baseStats = [
            'id' => 'stats_' . now()->timestamp,
            'generated_at' => now()->toIso8601String(),
            'total_patients' => Patient::count(),
            'today_appointments' => Appointment::whereDate('appointment_date', today())->count(),
            'pending_admissions' => Admission::where('status', 'pending')->count(),
            'occupied_beds' => Bed::where('is_occupied', true)->count(),
            'total_beds' => Bed::count(),
            'low_stock_medications' => Medication::whereColumn('stock_quantity', '<=', 'min_stock_level')->count(),
            'pending_lab_requests' => LabRequest::where('status', 'pending')->count(),
            'pending_prescriptions' => Prescription::where('status', 'pending')->count(),
        ];

        if ($userId) {
            $user = User::find($userId);
            if ($user) {
                $roleStats = match ($user->role) {
                    'doctor' => [
                        'my_today_appointments' => Appointment::where('doctor_id', $userId)->whereDate('appointment_date', today())->count(),
                        'my_pending_consultations' => Appointment::where('doctor_id', $userId)->where('status', 'In Consultation')->count(),
                        'my_completed_today' => MedicalRecord::where('doctor_id', $userId)->whereDate('created_at', today())->where('finalized', true)->count(),
                    ],
                    'pharmacist' => [
                        'dispensed_today' => Dispensation::whereDate('dispensed_at', today())->count(),
                    ],
                    'lab-technician' => [
                        'my_pending_tests' => LabRequest::where('lab_tech_id', $userId)->where('status', 'processing')->count(),
                        'completed_today' => LabRequest::where('status', 'completed')->whereDate('updated_at', today())->count(),
                    ],
                    'nurse' => [
                        'assigned_patients' => Admission::where('status', 'admitted')->count(),
                    ],
                    default => []
                };
                $baseStats = array_merge($baseStats, $roleStats);
            }
        }

        return ['documents' => [$baseStats], 'hasMore' => false, 'lastDoc' => $baseStats];
    }

    protected function pullBedOccupancy(array $checkpoint, int $limit, ?int $userId): array
    {
        $stats = [
            'id' => 'bed_stats_' . now()->timestamp,
            'total' => Bed::count(),
            'occupied' => Bed::where('is_occupied', true)->count(),
            'available' => Bed::where('is_occupied', false)->count(),
            'by_ward' => Ward::withCount(['beds', 'beds as occupied_count' => fn($q) => $q->where('is_occupied', true)])
                ->get()
                ->map(fn($w) => [
                    'ward_id' => (string) $w->id,
                    'ward_name' => $w->name,
                    'total' => (int) $w->beds_count,
                    'occupied' => (int) $w->occupied_count,
                ]),
        ];

        return ['documents' => [$stats], 'hasMore' => false, 'lastDoc' => $stats];
    }

    protected function pullLowStockAlerts(array $checkpoint, int $limit, ?int $userId): array
    {
        $docs = Medication::whereColumn('stock_quantity', '<=', 'min_stock_level')
            ->orWhere('stock_quantity', 0)
            ->select('id', 'name', 'stock_quantity', 'min_stock_level', 'updated_at')
            ->orderBy('stock_quantity')
            ->limit($limit + 1)
            ->get();

        return $this->formatResponse($docs, $limit, fn($m) => [
            'id' => (string) $m->id,
            'name' => $m->name,
            'current' => (int) $m->stock_quantity,
            'minimum' => (int) $m->min_stock_level,
            'critical' => $m->stock_quantity === 0,
        ]);
    }

    protected function pullDoctors(array $checkpoint, int $limit, ?int $userId): array
    {
        $docs = User::where('role', 'doctor')
            ->where('is_active', true)
            ->select('id', 'name', 'department_id', 'updated_at')
            ->orderBy('name')
            ->get();

        return ['documents' => $docs->map(fn($d) => [
            'id' => (string) $d->id,
            'name' => $d->name,
            'department_id' => $d->department_id ? (string) $d->department_id : null,
        ])->toArray(), 'hasMore' => false, 'lastDoc' => null];
    }

    protected function pullLabTechnicians(array $checkpoint, int $limit, ?int $userId): array
    {
        $docs = User::where('role', 'lab-technician')
            ->where('is_active', true)
            ->select('id', 'name', 'updated_at')
            ->orderBy('name')
            ->get();

        return ['documents' => $docs->map(fn($u) => [
            'id' => (string) $u->id,
            'name' => $u->name,
        ])->toArray(), 'hasMore' => false, 'lastDoc' => null];
    }

    protected function pullBedsOccupied(array $checkpoint, int $limit, ?int $userId): array
    {
        $docs = Bed::where('is_occupied', true)
            ->with('ward:id,name')
            ->with(['admissions' => fn($q) => $q->where('status', 'admitted')->with('patient:id,first_name,last_name')])
            ->where(function ($q) use ($checkpoint) {
                $q->where('updated_at', '>', $checkpoint['updated_at']);
            })
            ->limit($limit + 1)
            ->get();

        return $this->formatResponse($docs, $limit, fn($b) => [
            'id' => (string) $b->id,
            'number' => $b->bed_number,
            'ward_id' => (string) $b->ward_id,
            'ward_name' => $b->ward->name ?? '',
            'patient_id' => $b->admissions->first() ? (string) $b->admissions->first()->patient_id : null,
            'patient_name' => $b->admissions->first() && $b->admissions->first()->patient
                ? $b->admissions->first()->patient->first_name . ' ' . $b->admissions->first()->patient->last_name
                : '',
        ]);
    }
    protected function pullPatientsRecent(array $checkpoint, int $limit, ?int $userId): array
    {
        $docs = Patient::where(function ($q) use ($checkpoint) {
            $q->where('updated_at', '>', $checkpoint['updated_at'])
                ->orWhere(function ($sub) use ($checkpoint) {
                    $sub->where('updated_at', '=', $checkpoint['updated_at'])
                        ->where('id', '>', $checkpoint['id']);
                });
        })
            ->whereDate('created_at', '>=', now()->subDays(7)) // Only recent patients (last 7 days)
            ->orWhereDate('updated_at', '>=', now()->subDays(2)) // Or recently updated
            ->select('id', 'patient_uid', 'first_name', 'last_name', 'phone', 'email', 'gender', 'dob', 'created_at', 'updated_at')
            ->orderBy('created_at', 'desc')
            ->limit($limit + 1)
            ->get();

        return $this->formatResponse($docs, $limit, fn($p) => [
            'id' => (string) $p->id,
            'uid' => $p->patient_uid,
            'first_name' => $p->first_name,
            'last_name' => $p->last_name,
            'phone' => $p->phone,
            'email' => $p->email,
            'gender' => $p->gender,
            'dob' => $p->dob?->toDateString(),
            'created_at' => $p->created_at?->toDateTimeString(),
        ]);
    }
    protected function pullVitalsRecent(array $checkpoint, int $limit, ?int $userId): array
    {
        $docs = Vital::whereDate('created_at', '>=', now()->subDays(3))
            ->with('patient:id,first_name,last_name')
            ->where(function ($q) use ($checkpoint) {
                $q->where('created_at', '>', $checkpoint['updated_at'])
                    ->orWhere(function ($sub) use ($checkpoint) {
                        $sub->where('created_at', '=', $checkpoint['updated_at'])
                            ->where('id', '>', $checkpoint['id']);
                    });
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit + 1)
            ->get();

        return $this->formatResponse($docs, $limit, fn($v) => [
            'id' => (string) $v->id,
            'patient_id' => (string) $v->patient_id,
            'patient_name' => $v->patient ? $v->patient->first_name . ' ' . $v->patient->last_name : '',
            'temperature' => $v->temperature_celsius,
            'bp_systolic' => $v->blood_pressure_systolic,
            'bp_diastolic' => $v->blood_pressure_diastolic,
            'heart_rate' => $v->heart_rate_bpm,
            'spo2' => $v->spo2_percentage,
            'recorded_at' => $v->recorded_at?->toDateTimeString(),
        ], true);
    }

    /**
     * ============================================
     * CORE INFRASTRUCTURE
     * ============================================
     */

    protected function executePull(array $collections, array $checkpoint, ?int $userId): JsonResponse
    {
        $response = [
            'checkpoint' => $checkpoint,
            'documents' => [],
            'hasMore' => false,
        ];

        foreach ($collections as $collection) {
            $method = $this->getPullMethod($collection);
            if (!method_exists($this, $method)) {
                $response['documents'][$collection] = [];
                continue;
            }

            $result = $this->$method($checkpoint, self::BATCH_SIZE, $userId);
            $response['documents'][$collection] = $result['documents'];

            if ($result['hasMore']) {
                $response['hasMore'] = true;
            }
            if (!empty($result['documents'])) {
                $response['checkpoint'] = $this->advanceCheckpoint($response['checkpoint'], $result['lastDoc']);
            }
        }

        return response()->json($response);
    }

    protected function getPullMethod(string $collection): string
    {
        $map = [
            'appointments_today' => 'pullAppointmentsToday',
            'admissions_active' => 'pullAdmissionsActive',
            'admissions_pending' => 'pullAdmissionsPending',
            'patients_recent' => 'pullPatientsRecent',
            'recent_patients' => 'pullPatientsRecent',
            'my_appointments_today' => 'pullMyAppointmentsToday',
            'my_appointments_week' => 'pullMyAppointmentsWeek',
            'my_patients_list' => 'pullMyPatientsList',
            'medical_records_finalized' => 'pullMedicalRecordsFinalized',
            'pending_prescriptions' => 'pullPendingPrescriptions',
            'pending_requests' => 'pullPendingRequests',
            'my_assigned_requests' => 'pullMyAssignedRequests',
            'admitted_patients' => 'pullAdmittedPatients',
            'beds_occupied' => 'pullBedsOccupied',
            'bed_occupancy' => 'pullBedOccupancy',
            'low_stock_alerts' => 'pullLowStockAlerts',
            'supply_usages_recent' => 'pullSupplyUsagesRecent',
            'vitals_recent' => 'pullVitalsRecent',
            'lab_definitions' => 'pullLabDefinitions',
            'lab_technicians' => 'pullLabTechnicians',
            'dashboard_stats' => 'pullDashboardStats',
        ];

        return $map[$collection] ?? ('pull' . $this->studlyCase($collection));
    }

    protected function formatResponse($docs, int $limit, callable $mapper, bool $isWorkflow = false): array
    {
        $hasMore = $docs->count() > $limit;
        $documents = $docs->take($limit)->map(function ($doc) use ($mapper, $isWorkflow) {
            $data = $mapper($doc);
            $data['updated_at'] = $doc->updated_at?->toIso8601String() ?? now()->toIso8601String();
            if ($isWorkflow) {
                $data['isReadOnly'] = true;
                $data['isWorkflow'] = true;
            }
            return $data;
        })->toArray();

        return [
            'documents' => $documents,
            'hasMore' => $hasMore,
            'lastDoc' => $documents[count($documents) - 1] ?? null
        ];
    }

    protected function handlePush(Request $request, array $allowedCollections, string $role): JsonResponse
    {
        $collection = $request->input('collection');
        $changes = $request->input('changes', []);

        if (in_array($collection, self::WORKFLOW_COLLECTIONS)) {
            return response()->json([
                'success' => false,
                'message' => "'{$collection}' is read-only via sync. Use real-time API.",
                'written' => [],
                'conflicts' => []
            ], 403);
        }

        if (!in_array($collection, $allowedCollections)) {
            return response()->json([
                'success' => false,
                'message' => "Push not allowed for '{$collection}'",
                'written' => [],
                'conflicts' => []
            ], 403);
        }

        if (count($changes) > self::MAX_PUSH_SIZE) {
            return $this->error(null, 'Batch size exceeds maximum', 413);
        }

        $written = [];
        $conflicts = [];

        DB::beginTransaction();
        try {
            foreach ($changes as $change) {
                $table = $this->collectionToTable($collection);
                $existing = DB::table($table)->find($change['id'] ?? 0);

                if ($existing && isset($change['updated_at'])) {
                    if (strtotime($existing->updated_at) > strtotime($change['updated_at'])) {
                        $conflicts[] = [
                            'id' => $change['id'],
                            'status' => 'conflict',
                            'reason' => 'Server version newer',
                            'server_rev' => $existing->updated_at
                        ];
                        continue;
                    }
                }

                $data = collect($change)
                    ->except(['id', '_rev', 'isReadOnly', 'isWorkflow', 'collection', 'updated_at'])
                    ->toArray();
                $data['updated_at'] = now();

                if ($existing) {
                    DB::table($table)->where('id', $change['id'])->update($data);
                    $written[] = (string) $change['id'];
                } else {
                    $data['created_at'] = now();
                    $id = DB::table($table)->insertGetId($data);
                    $written[] = (string) $id;
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sync push failed', ['error' => $e->getMessage()]);
            return $this->error(null, 'Push failed: ' . $e->getMessage(), 500);
        }

        return response()->json(['success' => true, 'written' => $written, 'conflicts' => $conflicts]);
    }

    protected function readOnlyResponse(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Sync is read-only for this role. Use real-time API.',
            'written' => [],
            'conflicts' => []
        ], 403);
    }

    protected function parseCheckpoint($input): array
    {
        if (is_string($input)) $input = json_decode($input, true);
        return $input ?? ['updated_at' => '1970-01-01 00:00:00', 'id' => 0];
    }

    protected function advanceCheckpoint(array $current, ?array $lastDoc): array
    {
        if (!$lastDoc) return $current;
        return [
            'updated_at' => $lastDoc['updated_at'] ?? $current['updated_at'],
            'id' => (int) ($lastDoc['id'] ?? $current['id'])
        ];
    }

    protected function studlyCase(string $str): string
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $str)));
    }

    protected function collectionToTable(string $collection): string
    {
        return match ($collection) {
            'users' => 'users',
            'patients' => 'patients',
            'medications' => 'medications',
            'shifts' => 'user_shifts',
            'departments' => 'departments',
            'wards' => 'wards',
            'beds' => 'beds',
            'bed_types' => 'bed_types',
            'lab_test_definitions' => 'lab_test_definitions',
            'supplies' => 'supplies',
            default => $collection
        };
    }
}
