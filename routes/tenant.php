<?php

declare(strict_types=1);

use App\Livewire\Auth\ForgotPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Models\User;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

// -- Auth Components --
use App\Livewire\Auth\Login;
use App\Livewire\Tenants\Auth\TenantResetPassword;

// -- Admin Components --
use App\Livewire\Tenants\Admin\Index as AdminIndex;
use App\Livewire\Tenants\Admin\UserManagement;
use App\Livewire\Tenants\Admin\Settings;
use App\Livewire\Tenants\Admin\AiAssistant;
use App\Livewire\Tenants\Admin\CreateNewUser;
use App\Livewire\Tenants\Admin\UserActivities;
use App\Livewire\Tenants\Admin\SendAdminFeedback;
use App\Livewire\Tenants\Admin\Profile as AdminProfile;
use App\Livewire\Tenants\Admin\Shifts;
use App\Livewire\Tenants\Admin\RevenueDashboard;
use App\Livewire\Tenants\Admin\AdminFeedbackHistory;
// -- Doctor Components --
use App\Livewire\Tenants\Doctor\Index as DoctorIndex;
use App\Livewire\Tenants\Doctor\Patient;
use App\Livewire\Tenants\Doctor\DoctorAppointment;
use App\Livewire\Tenants\Doctor\MedicalRecord;
use App\Livewire\Tenants\Doctor\MedicalExplainer;
use App\Livewire\Tenants\Doctor\ViewPatientInfo;
use App\Livewire\Tenants\Doctor\LabTestAndPrescription;
use App\Livewire\Tenants\Doctor\Feedbacks as DoctorFeedbacks;
use App\Livewire\Tenants\Doctor\SendFeedback as DoctorSendFeedback;
use App\Livewire\Tenants\Doctor\Profile as DoctorProfile;
use App\Livewire\Tenants\Doctor\DoctorLabRequest;

// -- Receptionist Components --
use App\Livewire\Tenants\Receptionist\Index as ReceptionistIndex;
use App\Livewire\Tenants\Receptionist\RegisterPatient;
use App\Livewire\Tenants\Receptionist\BookAppointment;
use App\Livewire\Tenants\Receptionist\Appointments as ReceptionistAppointments;
use App\Livewire\Tenants\Receptionist\Patients as ReceptionistPatients;
use App\Livewire\Tenants\Receptionist\Profile as ReceptionistProfile;
use App\Livewire\Tenants\Receptionist\Checkin;
use App\Livewire\Tenants\Receptionist\AdmitPatient;
use App\Livewire\Tenants\Receptionist\ViewAdmissionDetails;
use App\Livewire\Tenants\Receptionist\ReceptionistFeedBack;
use App\Livewire\Tenants\Receptionist\Feedbacks as ReceptionistFeedbacksHistory;

// -- Lab Technician Components --
use App\Livewire\Tenants\LabTechnician\Index as LabTechnicianIndex;
use App\Livewire\Tenants\LabTechnician\LabResult;
use App\Livewire\Tenants\LabTechnician\EnterResults;
use App\Livewire\Tenants\LabTechnician\TestRequest;
use App\Livewire\Tenants\LabTechnician\ManageLabTestDefinitions;
use App\Livewire\Tenants\LabTechnician\CreateLabTestDefinition;
use App\Livewire\Tenants\LabTechnician\Feedbacks as LabTechnicianFeedbacks;
use App\Livewire\Tenants\LabTechnician\SendFeedback as LabTechnicianSendFeedback;
use App\Livewire\Tenants\LabTechnician\Profile as LabTechnicianProfile;

// -- Nurse Components --
use App\Livewire\Tenants\Nurse\Dashboard as NurseDashboard;
use App\Livewire\Tenants\Nurse\SupplyUsage;
use App\Livewire\Tenants\Nurse\RecordVitals;
use App\Livewire\Tenants\Nurse\Profile as NurseProfile;
use App\Livewire\Tenants\Nurse\Feedbacks as NurseFeedbacks;
use App\Livewire\Tenants\Nurse\SendFeedback as NurseSendFeedback;

// -- Pharmacist Components --
use App\Livewire\Tenants\Pharmacist\Dashboard as PharmacistDashboard;
use App\Livewire\Tenants\Pharmacist\FeedBackRequestList;
use App\Livewire\Tenants\Pharmacist\SubmitFeedBack;
use App\Livewire\Tenants\Pharmacist\ManageDrugsInventory;
use App\Livewire\Tenants\Pharmacist\CreateDrugs;
use App\Livewire\Tenants\Pharmacist\SalesReport;
use App\Livewire\Tenants\Pharmacist\Medications;
use App\Livewire\Tenants\Pharmacist\Profile as PharmacistProfile;
use Illuminate\Support\Facades\Cache;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| All routes within this group are scoped to the specific tenant domain.
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {

    // =========================================================================
    // Public / Guest Routes
    // =========================================================================

    // Landing Page
    Route::get('/', function () {
        return 'This is your multi-tenant application. Tenant ID: ' . tenant('id') . ' | Users: ' . User::count();
    })->name('tenant.root');

    // Language Switching (Accessible to guests and auth users)
    Route::get('/tenant/language/{locale}', function (string $locale) {
        if (!in_array($locale, ['en', 'es', 'fr'])) {
            abort(400);
        }
        Session::put('locale', $locale);
        return redirect()->back();
    })->name('tenant.language.switch');

    // Authentication Routes (Guest Only)
    Route::middleware('guest')->prefix('tenant')->group(function () {
        Route::get('/login', Login::class)->name('tenant.login');
        Route::get('/forgot-password', ForgotPassword::class)->name('tenant.password.request');
        Route::get('/reset-password/{token}', TenantResetPassword::class)->name('tenant.password.reset');
    });

    // =========================================================================
    // Authenticated Routes
    // =========================================================================
    Route::middleware(['auth'])->group(function () {

    // 1. Heartbeat: Tells the server "I am online"
    Route::post('/user/heartbeat', function (Request $request) {
        // Store 'true' in cache for 40 seconds (we will ping every 30s)
        Cache::put('user-online-' . $request->user()->id, true, 40);
        return response()->noContent();
    });

    // 2. Fetch Missed: Gets notifications saved while user was offline
    Route::get('/user/notifications/missed', function (Request $request) {
        // Get unread notifications
        $notifications = $request->user()->unreadNotifications;

        // Mark them as read immediately since we are moving them to Frontend LocalStorage
        $request->user()->unreadNotifications->markAsRead();

        return $notifications;
    });

    // 3. Mark specific notification as read (if needed)
    Route::post('/user/notifications/{id}/read', function (Request $request, $id) {
        $request->user()->notifications()->where('id', $id)->first()?->markAsRead();
        return response()->noContent();
    });
        // Logout
        Route::post('/logout', function (Request $request) {
            Auth::guard('web')->logout();
            Session::invalidate();
            Session::regenerateToken();
            return redirect()->route('tenant.login');
        })->name('auth.logout');

        // Central Dashboard Redirector
        // Redirects users to their specific dashboard based on role
        Route::get('/dashboard', function () {
            $user = Auth::user();
            Log::info('Accessing /dashboard route', ['user_id' => $user->id, 'user_role' => $user->role]);

            return match ($user->role) {
                'admin'          => redirect()->route('admin.dashboard'),
                'doctor'         => redirect()->route('doctor.dashboard'),
                'nurse'          => redirect()->route('nurse.dashboard'),
                'lab-technician' => redirect()->route('lab-technician.dashboard'),
                'pharmacist'     => redirect()->route('pharmacist.dashboard'),
                'receptionist'   => redirect()->route('receptionist.dashboard'),
                default          => redirect('/'),
            };
        })->name('dashboard');

        // ---------------------------------------------------------------------
        // Role-Based Routes
        // ---------------------------------------------------------------------

        // 1. Admin
        Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
            Route::get('/dashboard', AdminIndex::class)->name('dashboard');
            Route::get('/user-management', UserManagement::class)->name('user-management');
            Route::get('/settings', Settings::class)->name('settings');
            Route::get('/ai-assistant', AiAssistant::class)->name('ai-assistant');
            Route::get('/create-new-user', CreateNewUser::class)->name('create-new-user');
            Route::get('/user-activities', UserActivities::class)->name('user-activities');
            Route::get('/send-feedback', SendAdminFeedback::class)->name('admin-feedback');
            Route::get('/profile', AdminProfile::class)->name('profile');
            Route::get('/user-shifts', Shifts::class)->name('user-shifts');
            Route::get('/revenue-report', RevenueDashboard::class)->name('revenue-report');
            Route::get('/feedback-history', AdminFeedbackHistory::class)->name('feedback-history');
        });

        // 2. Doctor
        Route::middleware('role:doctor')->prefix('doctor')->name('doctor.')->group(function () {
            Route::get('/dashboard', DoctorIndex::class)->name('dashboard');
            Route::get('/patients', Patient::class)->name('patients');
            Route::get('/appointments', DoctorAppointment::class)->name('appointments');
            Route::get('/medical-records', MedicalRecord::class)->name('medical-records');
            Route::get('/medical-explainer', MedicalExplainer::class)->name('medical-explainer');
            Route::get('/patient-info/{patient}', ViewPatientInfo::class)->name('patient-info');
            Route::get('/consultation/{consultationId}', LabTestAndPrescription::class)->name('consultation');
            Route::get('/feedbacks', DoctorFeedbacks::class)->name('feedbacks');
            Route::get('/send-feedback', DoctorSendFeedback::class)->name('send-feedback');
            Route::get('/profile', DoctorProfile::class)->name('profile');
            Route::get('/lab-request', DoctorLabRequest::class)->name('lab-request');
        });

        // 3. Receptionist
        Route::middleware('role:receptionist')->prefix('receptionist')->name('receptionist.')->group(function () {
            Route::get('/dashboard', ReceptionistIndex::class)->name('dashboard');
            Route::get('/register-patient', RegisterPatient::class)->name('register-patient');
            Route::get('/book-appointment', BookAppointment::class)->name('book-appointment');
            Route::get('/appointments', ReceptionistAppointments::class)->name('appointments');
            Route::get('/patients', ReceptionistPatients::class)->name('patients');
            Route::get('/profile', ReceptionistProfile::class)->name('profile');
            Route::get('/checkin', Checkin::class)->name('checkin');
            Route::get('/admit-patient/{admission}', AdmitPatient::class)->name('admit-patient');
            Route::get('/nurse/admission-details/{patient}', ViewAdmissionDetails::class)->name('view-admission-details');
            Route::get('/send-feedback', ReceptionistFeedBack::class)->name('receptionist-feedback');
            Route::get('/feedback-history', ReceptionistFeedbacksHistory::class)->name('feedback-history');
        });

        // 4. Lab Technician
        Route::middleware('role:lab-technician')->prefix('lab-technician')->name('lab-technician.')->group(function () {
            Route::get('/dashboard', LabTechnicianIndex::class)->name('dashboard');
            Route::get('/lab-results', LabResult::class)->name('lab-results');
            Route::get('/enter-results/{labRequest}', EnterResults::class)->name('enter-results');
            Route::get('/test-requests', TestRequest::class)->name('test-requests');
            Route::get('/manage-lab-definitions', ManageLabTestDefinitions::class)->name('manage-lab-definitions');
            Route::get('/create-lab-definitions', CreateLabTestDefinition::class)->name('create-lab-definitions');
            Route::get('/feedbacks', LabTechnicianFeedbacks::class)->name('feedbacks');
            Route::get('/send-feedback', LabTechnicianSendFeedback::class)->name('send-feedback');
            Route::get('/profile', LabTechnicianProfile::class)->name('profile');
        });

        // 5. Nurse
        Route::middleware('role:nurse')->prefix('nurse')->name('nurse.')->group(function () {
            Route::get('/dashboard', NurseDashboard::class)->name('dashboard');
            Route::get('/medical-usage', SupplyUsage::class)->name('medical-usage');
            Route::get('/record-vitals', RecordVitals::class)->name('record-vitals');
            Route::get('/profile', NurseProfile::class)->name('profile');
            Route::get('/feedbacks', NurseFeedbacks::class)->name('feedbacks');
            Route::get('/send-feedback', NurseSendFeedback::class)->name('send-feedback');
        });

        // 6. Pharmacist
        Route::middleware('role:pharmacist')->prefix('pharmacist')->name('pharmacist.')->group(function () {
            Route::get('/dashboard', PharmacistDashboard::class)->name('dashboard');
            Route::get('/feedbacks', FeedBackRequestList::class)->name('feedbacks');
            Route::get('/submit-feedback', SubmitFeedBack::class)->name('submit-feedback');
            Route::get('/manage-drugs', ManageDrugsInventory::class)->name('manage-drugs');
            Route::get('/create-drugs', CreateDrugs::class)->name('create-drugs');
            Route::get('/sales-report', SalesReport::class)->name('sales-report');
            Route::get('/medications', Medications::class)->name('medications');
            Route::get('/profile', PharmacistProfile::class)->name('profile');
        });
    });
});
