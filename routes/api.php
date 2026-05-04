<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Tenants\Admin\AdminDashboardController;
use App\Http\Controllers\Api\Tenants\Admin\AdminRevenueController;
use App\Http\Controllers\Api\Tenants\Admin\AdminSettingsController;
use App\Http\Controllers\Api\Tenants\Admin\AdminShiftController;
use App\Http\Controllers\Api\Tenants\Admin\AdminUserController;
use App\Http\Controllers\Api\Tenants\Admin\UserActivityController;
use App\Http\Controllers\Api\Tenants\Doctor\AppointmentController as DoctorAppointmentController;
use App\Http\Controllers\Api\Tenants\Doctor\DoctorDashboardController;
use App\Http\Controllers\Api\Tenants\Doctor\LabRequestController as DoctorLabRequestController;
use App\Http\Controllers\Api\Tenants\Doctor\MedicalRecordApiController;
use App\Http\Controllers\Api\Tenants\Doctor\PatientController as DoctorPatientController;
use App\Http\Controllers\Api\Tenants\Receptionist\AdmissionController;
use App\Http\Controllers\Api\Tenants\Receptionist\AppointmentController;
use App\Http\Controllers\Api\Tenants\Receptionist\PatientController;
use App\Http\Controllers\Api\Tenants\Receptionist\ReceptionistDashboardController;
use App\Http\Controllers\Api\Tenants\SyncController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/ping', fn () => response()->noContent());
Route::post('/login', [LoginController::class, 'login']);
Broadcast::routes([ 'middleware' => ['auth:sanctum','tenant.auth']]);

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES (Authenticated & Tenant Verified)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'tenant.auth'])->group(function () {

    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/user', fn (Request $request) => $request->user());

    /*
    |--------------------------------------------------------------------------
    | SYNC API ROUTES (RxDB/Dexie Optimized)
    |--------------------------------------------------------------------------
    */
    Route::prefix('sync')->name('sync.')->controller(SyncController::class)->group(function () {

        // Admin Sync
        Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
            Route::post('/pull', 'adminPull')->name('pull');
            Route::post('/push', 'adminPush')->name('push');
        });

        // Receptionist Sync
        Route::middleware('role:receptionist')->prefix('receptionist')->name('receptionist.')->group(function () {
            Route::post('/pull', 'receptionistPull')->name('pull');
            Route::post('/push', 'receptionistPush')->name('push');
        });

        // Doctor Sync
        Route::middleware('role:doctor')->prefix('doctor')->name('doctor.')->group(function () {
            Route::post('/pull', 'doctorPull')->name('pull');
            Route::post('/push', 'doctorPush')->name('push');
        });

        // Lab Technician Sync
        Route::middleware('role:lab-technician')->prefix('lab-technician')->name('lab-technician.')->group(function () {
            Route::post('/pull', 'labTechnicianPull')->name('pull');
            Route::post('/push', 'labTechnicianPush')->name('push');
        });

        // Nurse Sync
        Route::middleware('role:nurse')->prefix('nurse')->name('nurse.')->group(function () {
            Route::post('/pull', 'nursePull')->name('pull');
            Route::post('/push', 'nursePush')->name('push');
        });

        // Pharmacist Sync
        Route::middleware('role:pharmacist')->prefix('pharmacist')->name('pharmacist.')->group(function () {
            Route::post('/pull', 'pharmacistPull')->name('pull');
            Route::post('/push', 'pharmacistPush')->name('push');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | 0. ADMIN ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        // FIXED: Renamed to 'api-dashboard' to avoid collision with web.php 'admin.dashboard'
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('api-dashboard');
        Route::apiResource('shifts', AdminShiftController::class);

        // Standard CRUD
        Route::apiResource('users', AdminUserController::class);
        Route::apiResource('user-activities', UserActivityController::class);
        // Custom Actions
        Route::post('users/{user}/resend-invitation', [AdminUserController::class, 'resendInvitation']);
        Route::get('available-shifts', [AdminUserController::class, 'availableShifts']);
        Route::get('/attachments/{attachment}/preview', [AdminUserController::class, 'previewImage'])->name('attachments.preview');
        // Admin Settings Routes
        Route::prefix('settings')->group(function () {

            // Helper to get dropdown options (Departments, Wards, BedTypes)
            Route::get('/options', [AdminSettingsController::class, 'options']);

            // Dynamic Resource Routes
            // Matches: /api/admin/settings/department, /api/admin/settings/bed, etc.
            Route::get('/{type}', [AdminSettingsController::class, 'index']);
            Route::post('/{type}', [AdminSettingsController::class, 'store']);
            Route::put('/{type}/{id}', [AdminSettingsController::class, 'update']);
            Route::delete('/{type}/{id}', [AdminSettingsController::class, 'destroy']);
        })->where('type', 'department|ward|bed-type|bed|supply');
        // Revenue Analytics
        Route::get('/revenue', [AdminRevenueController::class, 'index'])->name('revenue');
    });

    /*
    |--------------------------------------------------------------------------
    | 1. RECEPTIONIST ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:receptionist')->prefix('receptionist')->name('receptionist.')->group(function () {
        // FIXED: Renamed to 'api-dashboard' to avoid collision with web.php 'receptionist.dashboard'
        Route::get('/dashboard', [ReceptionistDashboardController::class, 'index'])->name('api-dashboard');

        // Patient Management
        Route::get('/patients/search', [PatientController::class, 'search'])->name('patients.search');
        Route::apiResource('patients', PatientController::class);

        // Appointment Management
        Route::apiResource('appointments', AppointmentController::class);
        Route::patch('/admissions/{admission}/discharge', [AdmissionController::class, 'discharge'])->name('admissions.discharge');

        // Admissions & Bed Management
        Route::get('/patients/{patient}/admissions', [AdmissionController::class, 'history'])->name('admissions.history');
        Route::get('/beds/available', [AdmissionController::class, 'checkAvailability'])->name('beds.available');
        Route::apiResource('admissions', AdmissionController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | 2. DOCTOR ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:doctor')->prefix('doctor')->name('doctor.')->group(function () {

        // Dashboard
        Route::get('/dashboard', [DoctorDashboardController::class, 'index'])->name('api-dashboard');

        // Appointment Lifecycle
        Route::controller(DoctorAppointmentController::class)->prefix('appointments')->group(function () {
            Route::get('/', 'index');
            Route::patch('/{appointment}/start', 'start');
            Route::patch('/{appointment}/end', 'end');
        });

        // Patient Lists & Lab Orders View
        Route::get('/patients', [DoctorPatientController::class, 'index']);
        Route::get('/lab-requests', [DoctorLabRequestController::class, 'index']);

        // Medical Records & Consultations
        Route::controller(MedicalRecordApiController::class)->group(function () {
            Route::get('/consultations/{id}', 'showConsultation');
            Route::get('/patients/{patient}', 'show'); // Patient summary
            Route::post('/patients/{patient}/admit', 'admit');

            Route::prefix('medical-records')->group(function () {
                Route::get('/context/{patientId}', 'getConsultationContext'); // Pre-fill data
                Route::post('/', 'store'); // Create new record

                // Form Helpers
                Route::get('/medications', 'medications');
                Route::get('/lab-definitions', 'labDefinitions');
                Route::get('/lab-technicians', 'labTechnicians');
            });
        });
    });

    /*
    |--------------------------------------------------------------------------
    | 3. LAB TECHNICIAN ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:lab-technician')->prefix('lab-technician')->name('lab-technician.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\Api\Tenants\LabTechnician\LabTechnicianDashboardController::class, 'index'])->name('api-dashboard');

        // Lab Requests
        Route::get('/lab-requests', [App\Http\Controllers\Api\Tenants\LabTechnician\LabTechnicianController::class, 'getLabRequests'])->name('lab-requests.index');
        Route::patch('/lab-requests/{labRequest}/start', [App\Http\Controllers\Api\Tenants\LabTechnician\LabTechnicianController::class, 'startRequest'])->name('lab-requests.start');

        // Lab Results
        Route::get('/lab-results', [App\Http\Controllers\Api\Tenants\LabTechnician\LabTechnicianController::class, 'getLabResults'])->name('lab-results.index');
        Route::post('/lab-requests/{labRequest}/results', [App\Http\Controllers\Api\Tenants\LabTechnician\LabTechnicianController::class, 'submitResults'])->name('lab-results.store');
        Route::get('/lab-results/{labResult}/download', [App\Http\Controllers\Api\Tenants\LabTechnician\LabTechnicianController::class, 'downloadResult'])->name('lab-results.download');

        // Lab Test Definitions
        Route::get('/test-definitions', [App\Http\Controllers\Api\Tenants\LabTechnician\LabTechnicianController::class, 'getTestDefinitions'])->name('test-definitions.index');
        Route::post('/test-definitions', [App\Http\Controllers\Api\Tenants\LabTechnician\LabTechnicianController::class, 'createTestDefinition'])->name('test-definitions.store');
        Route::put('/test-definitions/{testDefinition}', [App\Http\Controllers\Api\Tenants\LabTechnician\LabTechnicianController::class, 'updateTestDefinition'])->name('test-definitions.update');
        Route::delete('/test-definitions/{testDefinition}', [App\Http\Controllers\Api\Tenants\LabTechnician\LabTechnicianController::class, 'deleteTestDefinition'])->name('test-definitions.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | 4. NURSE ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:nurse')->prefix('nurse')->name('nurse.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\Api\Tenants\Nurse\NurseDashboardController::class, 'index'])->name('api-dashboard');
    });

    /*
    |--------------------------------------------------------------------------
    | 5. PHARMACIST ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:pharmacist')->prefix('pharmacist')->name('pharmacist.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\Api\Tenants\Pharmacist\PharmacistDashboardController::class, 'index'])->name('api-dashboard');

        // Medications
        Route::get('/medications', [App\Http\Controllers\Api\Tenants\Pharmacist\PharmacistDashboardController::class, 'medications'])->name('api-medications');

        // Inventory Management
        Route::get('/inventory', [App\Http\Controllers\Api\Tenants\Pharmacist\PharmacistController::class, 'getMedications'])->name('inventory.index');
        Route::get('/inventory/{medication}', [App\Http\Controllers\Api\Tenants\Pharmacist\PharmacistController::class, 'getMedication'])->name('inventory.show');
        Route::post('/inventory', [App\Http\Controllers\Api\Tenants\Pharmacist\PharmacistController::class, 'createMedication'])->name('inventory.store');
        Route::put('/inventory/{medication}', [App\Http\Controllers\Api\Tenants\Pharmacist\PharmacistController::class, 'updateMedication'])->name('inventory.update');
        Route::delete('/inventory/{medication}', [App\Http\Controllers\Api\Tenants\Pharmacist\PharmacistController::class, 'deleteMedication'])->name('inventory.destroy');

        // Patients & Prescriptions
        Route::get('/patients', [App\Http\Controllers\Api\Tenants\Pharmacist\PharmacistController::class, 'getPatients'])->name('patients.index');
        Route::get('/patients/{patientId}/prescriptions', [App\Http\Controllers\Api\Tenants\Pharmacist\PharmacistController::class, 'getPatientPrescriptions'])->name('patients.prescriptions');
        Route::get('/prescriptions/{prescriptionId}', [App\Http\Controllers\Api\Tenants\Pharmacist\PharmacistController::class, 'getPrescription'])->name('prescriptions.show');
        Route::post('/prescriptions/{prescriptionId}/dispense', [App\Http\Controllers\Api\Tenants\Pharmacist\PharmacistController::class, 'dispenseItems'])->name('prescriptions.dispense');
    });

    /*
    |--------------------------------------------------------------------------
    | 6. GLOBAL / SHARED ROUTES
    |--------------------------------------------------------------------------
    */
    // File Previews (Shared by Doctor/Lab/Patient)
    Route::get('/attachments/{attachment}/preview', [MedicalRecordApiController::class, 'previewAttachment'])->name('attachments.preview');

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\Tenants\NotificationController::class, 'index'])->name('index');
        Route::get('/unread', [App\Http\Controllers\Api\Tenants\NotificationController::class, 'unread'])->name('unread');
        Route::get('/unread-count', [App\Http\Controllers\Api\Tenants\NotificationController::class, 'unreadCount'])->name('unread-count');
        Route::get('/{id}', [App\Http\Controllers\Api\Tenants\NotificationController::class, 'show'])->name('show');
        Route::post('/mark-all-read', [App\Http\Controllers\Api\Tenants\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::post('/{id}/mark-read', [App\Http\Controllers\Api\Tenants\NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::delete('/{id}', [App\Http\Controllers\Api\Tenants\NotificationController::class, 'destroy'])->name('destroy');
    });

});
