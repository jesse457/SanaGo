<?php

declare(strict_types=1);

// Import necessary classes and controllers for route handling
use App\Http\Controllers\SseController;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Tenants\Admin\AdminFeedbackHistory;
use App\Livewire\Tenants\Admin\AiAssistant;
use App\Livewire\Tenants\Admin\CreateNewUser;
use App\Livewire\Tenants\Admin\Index as AdminIndex; // Admin dashboard
use App\Livewire\Tenants\Admin\Profile as AdminProfile;
use App\Livewire\Tenants\Admin\RevenueDashboard;
use App\Livewire\Tenants\Admin\SendAdminFeedback;
use App\Livewire\Tenants\Admin\Settings;
use App\Livewire\Tenants\Admin\Shifts;
use App\Livewire\Tenants\Admin\UserActivities;
use App\Livewire\Tenants\Admin\UserManagement;
use App\Livewire\Tenants\Doctor\DoctorAppointment;
use App\Livewire\Tenants\Doctor\DoctorLabRequest;
use App\Livewire\Tenants\Doctor\Feedbacks as DoctorFeedbacks;
use App\Livewire\Tenants\Doctor\Index as DoctorIndex; // Doctor dashboard
use App\Livewire\Tenants\Doctor\LabTestAndPrescription;
use App\Livewire\Tenants\Doctor\MedicalExplainer;
use App\Livewire\Tenants\Doctor\MedicalRecord;
use App\Livewire\Tenants\Doctor\Patient;
use App\Livewire\Tenants\Doctor\Profile;
use App\Livewire\Tenants\Doctor\SendFeedback;
use App\Livewire\Tenants\Doctor\ViewPatientInfo;
use App\Livewire\Tenants\LabTechnician\CreateLabTestDefinition;
use App\Livewire\Tenants\LabTechnician\EnterResults;
use App\Livewire\Tenants\LabTechnician\Feedbacks as LabTechnicianFeedbacks;
use App\Livewire\Tenants\LabTechnician\Index as LabTechnicianIndex; // Lab technician dashboard
use App\Livewire\Tenants\LabTechnician\LabResult;
use App\Livewire\Tenants\LabTechnician\ManageLabTestDefinitions;
use App\Livewire\Tenants\LabTechnician\Profile as LabTechnicianProfile;
use App\Livewire\Tenants\LabTechnician\SendFeedback as LabTechnicianSendFeedback;
use App\Livewire\Tenants\LabTechnician\TestRequest;
use App\Livewire\Tenants\Nurse\Dashboard as NurseDashboard; // Nurse dashboard
use App\Livewire\Tenants\Nurse\Feedbacks as NurseFeedbacks;
use App\Livewire\Tenants\Nurse\Profile as NurseProfile;
use App\Livewire\Tenants\Nurse\RecordVitals;
use App\Livewire\Tenants\Nurse\SendFeedback as NurseSendFeedback;
use App\Livewire\Tenants\Nurse\SupplyUsage;
use App\Livewire\Tenants\Pharmacist\CreateDrugs;
use App\Livewire\Tenants\Pharmacist\Dashboard as PharmacistDashboard; // Pharmacist dashboard
use App\Livewire\Tenants\Pharmacist\FeedBackRequestList;
use App\Livewire\Tenants\Pharmacist\ManageDrugsInventory;
use App\Livewire\Tenants\Pharmacist\Medications;
use App\Livewire\Tenants\Pharmacist\Profile as PharmacistProfile;
use App\Livewire\Tenants\Pharmacist\SalesReport;
use App\Livewire\Tenants\Pharmacist\SubmitFeedBack;
use App\Livewire\Tenants\Receptionist\AdmitPatient;
use App\Livewire\Tenants\Receptionist\Appointments;
use App\Livewire\Tenants\Receptionist\BookAppointment;
use App\Livewire\Tenants\Receptionist\Checkin;
use App\Livewire\Tenants\Receptionist\Feedbacks;
use App\Livewire\Tenants\Receptionist\Index as ReceptionistIndex; // Receptionist dashboard
use App\Livewire\Tenants\Receptionist\Patients as ReceptionistPatients;
use App\Livewire\Tenants\Receptionist\Profile as ReceptionistProfile;
use App\Livewire\Tenants\Receptionist\ReceptionistFeedBack;
use App\Livewire\Tenants\Receptionist\RegisterPatient;
use App\Livewire\Tenants\Receptionist\ViewAdmissionDetails;
use App\Models\User; // User model for tenant info
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

// Group all tenant routes under required middleware for tenancy and web
Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {

    // --- Public/Guest Routes (Accessible without login or specific role) ---

    // Root route for tenants, shows tenant id and user count
    Route::get('/', function () {
        // Placeholder landing page
        return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id') . ' ' . User::all()->count() . ' users.';
    })->name('tenant.root');

  // Language switching route - should be outside tenant routes
        Route::get('/language/{locale}', function (string $locale) {
            if (! in_array($locale, ['en', 'es', 'fr'])) {
                abort(400);
            }
            Session::put('locale', $locale);
            return redirect()->back();
        })->name('language.switch');
    // Login page route
    Route::get('/login', Login::class)->name('tenant.login');
    Route::get('/forgot-password', ForgotPassword::class)->name('password.request');
    Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');

    // --- Authenticated Routes (Requires login, role middleware handles specific dashboards) ---
    Route::middleware(['auth'])->group(function () {

        // Dashboard route: redirects user to their role-specific dashboard
        Route::get('/dashboard', function () {
            $user = Auth::user();
            Log::info('Accessing /dashboard route', ['user_id' => $user->id, 'user_role' => $user->role]);
            // Redirect based on user role
            switch ($user->role) {
                case 'admin':
                    Log::info('Redirecting admin to admin.dashboard');
                    return redirect()->route('admin.dashboard');
                case 'doctor':
                    Log::info('Redirecting doctor to doctor.dashboard');
                    return redirect()->route('doctor.dashboard');
                case 'nurse':
                    Log::info('Redirecting nurse to nurse.dashboard');
                    return redirect()->route('nurse.dashboard');
                case 'lab-technician':
                    Log::info('Redirecting lab technician to lab-technician.dashboard');
                    return redirect()->route('lab-technician.dashboard');
                case 'pharmacist':
                    Log::info('Redirecting pharmacist to pharmacist.dashboard');
                    return redirect()->route('pharmacist.dashboard');
                case 'receptionist':
                    Log::info('Redirecting receptionist to receptionist.dashboard');
                    return redirect()->route('receptionist.dashboard');
                default:
                    Log::info('Redirecting to fallback / route');
                    return redirect('/'); // Fallback
            }
        })->name('dashboard');



        // Logout route: logs out user and redirects to login
        Route::post('/logout', function (Request $request) {
            Auth::guard('web')->logout();
        Session::invalidate();
        Session::regenerateToken();

        })->name('auth.logout');

        // --- Admin Routes ---
        Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
            Route::get('/dashboard', AdminIndex::class)->name('dashboard'); // Admin dashboard
            Route::get('/user-management', UserManagement::class)->name('user-management'); // User management
            Route::get('/settings', Settings::class)->name('settings'); // Settings
            Route::get('/ai-assistant', AiAssistant::class)->name('ai-assistant'); // AI assistant
            Route::get('/create-new-user', CreateNewUser::class)->name('create-new-user'); // Create user
            Route::get('/user-activities', UserActivities::class)->name('user-activities'); // User activities
            Route::get('/send-feedback', SendAdminFeedback::class)->name('admin-feedback'); // User activities
            Route::get('/profile', AdminProfile::class)->name('profile'); // Patients
            Route::get('/user-shifts', Shifts::class)->name('user-shifts'); // User activities
            Route::get('/revenue-report', RevenueDashboard::class)->name('revenue-report'); // Admin Feedback History
            Route::get('/feedback-history', AdminFeedbackHistory::class)->name('feedback-history'); // Admin Feedback History
        });

        // --- Doctor Routes ---
        Route::middleware('role:doctor')->prefix('doctor')->name('doctor.')->group(function () {
            Route::get('/dashboard', DoctorIndex::class)->name('dashboard'); // Doctor dashboard
            Route::get('/patients', Patient::class)->name('patients'); // List patients
            Route::get('/appointments', DoctorAppointment::class)->name('appointments'); // Appointments
            Route::get('/medical-records', MedicalRecord::class)->name('medical-records'); // Medical records
            Route::get('/medical-explainer', MedicalExplainer::class)->name('medical-explainer'); // Medical explainer
            Route::get('/patient-info/{patient}', ViewPatientInfo::class)->name('patient-info'); // View patient info
            Route::get('/consultation/{consultationId}', LabTestAndPrescription::class)->name('consultation'); // Doctor profile
            Route::get('/feedbacks', DoctorFeedbacks::class)->name('feedbacks'); // Doctor feedbacks
            Route::get('/send-feedback', SendFeedback::class)->name('send-feedback'); // Send feedback
            Route::get('/profile', Profile::class)->name('profile'); // List patients


            Route::get('/lab-request', DoctorLabRequest::class)->name('lab-request'); // List patients

        });
        // --- Receptionist Routes ---
        Route::middleware('role:receptionist')->prefix('receptionist')->name('receptionist.')->group(function () {
            Route::get('/dashboard', ReceptionistIndex::class)->name('dashboard'); // Receptionist dashboard
            Route::get('/register-patient', RegisterPatient::class)->name('register-patient'); // Register patient
            Route::get('/book-appointment', BookAppointment::class)->name('book-appointment'); // Book appointment
            Route::get('/appointments', Appointments::class)->name('appointments'); // Appointments
            Route::get('/patients', ReceptionistPatients::class)->name('patients'); // Patients
            Route::get('/profile', ReceptionistProfile::class)->name('profile'); // Patients
            Route::get('/checkin', Checkin::class)->name('checkin'); // Checkin
            Route::get('/admit-patient/{admission}', AdmitPatient::class)->name('admit-patient'); // Admit patient
            Route::get('/nurse/admission-details/{patient}', ViewAdmissionDetails::class)->name('view-admission-details'); // Admission de
            Route::get('/send-feedback', ReceptionistFeedBack::class)->name('receptionist-feedback'); // User activities
            Route::get('/feedback-history', Feedbacks::class)->name('feedback-history');
        });

        // --- Lab Technician Routes ---
        Route::middleware('role:lab-technician')->prefix('lab-technician')->name('lab-technician.')->group(function () {
            Route::get('/dashboard', LabTechnicianIndex::class)->name('dashboard'); // Lab tech dashboard
            Route::get('/lab-results', LabResult::class)->name('lab-results'); // Lab results
            Route::get('/enter-results/{labRequest}', EnterResults::class)->name('enter-results'); // Enter results
            Route::get('/test-requests', TestRequest::class)->name('test-requests'); // Test requests
            Route::get('/manage-lab-definitions', ManageLabTestDefinitions::class)->name('manage-lab-definitions'); // Enter lab definitions
            Route::get('/create-lab-definitions', CreateLabTestDefinition::class)->name('create-lab-definitions');
            Route::get('/feedbacks', LabTechnicianFeedbacks::class)->name('feedbacks'); // Lab technician feedbacks
            Route::get('/send-feedback', LabTechnicianSendFeedback::class)->name('send-feedback'); // Send feedback
            Route::get('/profile', LabTechnicianProfile::class)->name('profile'); // Lab tech profile
        });

        // --- Nurse Routes ---
        Route::middleware('role:nurse')->prefix('nurse')->name('nurse.')->group(function () {
            Route::get('/dashboard', NurseDashboard::class)->name('dashboard'); // Nurse dashboard
            Route::get('/medical-usage', SupplyUsage::class)->name('medical-usage'); // Medical usage
            Route::get('/record-vitals', RecordVitals::class)->name('record-vitals'); // Record vitalstails
            Route::get('/profile', NurseProfile::class)->name('profile'); // Lab tech profile
            Route::get('/feedbacks', NurseFeedbacks::class)->name('feedbacks'); // Lab technician feedbacks
            Route::get('/send-feedback', NurseSendFeedback::class)->name('send-feedback'); // Send feedback
        });

        // --- Pharmacist Routes ---
        Route::middleware('role:pharmacist')->prefix('pharmacist')->name('pharmacist.')->group(function () {
            Route::get('/dashboard', PharmacistDashboard::class)->name('dashboard'); // Pharmacist dashboard
            Route::get('/feedbacks', FeedBackRequestList::class)->name('feedbacks'); // Pharmacist dashboard
            Route::get('/submit-feedback', SubmitFeedBack::class)->name('submit-feedback'); // Pharmacist dashboard
            Route::get('/manage-drugs', ManageDrugsInventory::class)->name('manage-drugs'); // Pharmacist dashboard
            Route::get('/create-drugs', CreateDrugs::class)->name('create-drugs'); // Pharmacist dashboard
            Route::get('/sales-report', SalesReport::class)->name('sales-report'); // Sales report
            Route::get('/medications', Medications::class)->name('medications'); // Medications
            Route::get('/profile', PharmacistProfile::class)->name('profile'); // Lab tech profile
        });
    });
});
