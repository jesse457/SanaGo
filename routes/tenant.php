<?php

use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BedController;
use App\Http\Controllers\BedTypeController;
use App\Http\Controllers\CaisseController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DispensationController;
use App\Http\Controllers\EmergencyCaseController;
use App\Http\Controllers\ExpenditureController;
use App\Http\Controllers\FeedBackController;
use App\Http\Controllers\InfastructureController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LabRequestController;
use App\Http\Controllers\LabResultController;
use App\Http\Controllers\LabTestDefinitionController;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PatientInsuranceController;
use App\Http\Controllers\PayerController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\RadiologyRequestController;
use App\Http\Controllers\RadiologyResultController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RevenueAnalyticsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SupplyController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\SurgeryController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\UserActivityController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserShiftController;
use App\Http\Controllers\VitalController;
use App\Http\Controllers\WardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| TENANT ROUTES (Hospital Subdomains)
|--------------------------------------------------------------------------
| These routes only work on tenant subdomains.
| InitializeTenancyBySubdomain initializes tenancy when a subdomain is detected.
| On central domains (localhost, sanago.local), it does nothing.
| PreventAccessFromCentralDomains blocks central-domain access — ensures
| hospital routes only work on tenant subdomains.
|
| This file is auto-loaded by TenancyServiceProvider::mapRoutes().
*/

Route::middleware([
    'web',
    InitializeTenancyBySubdomain::class,
    PreventAccessFromCentralDomains::class,
    'throttle:api',
])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | AUTHENTICATED HOSPITAL ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth'])->group(function () {

        /*
         | Dashboards (Role & Permission-Based Views)
         | Admin → predefined full dashboard.
         | All other roles → role-agnostic dashboard built from permissions.
         */
        Route::prefix('dashboard')->group(function () {
            Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

            Route::get('/admin', [DashboardController::class, 'admin'])
                ->name('dashboard.admin')
                ->middleware('can:view-finance');
        });

        /*
| Patient Management & Admissions
*/
        Route::middleware('can:view-patients')->group(function () {
            Route::resource('patients', PatientController::class);
            Route::get('vitals/create/{patient}', [VitalController::class, 'create'])->name('vitals.create');

            Route::resource('vitals', VitalController::class)->except(['create']);
            Route::resource('admissions', AdmissionController::class)->except('create');
            Route::get('admissions/create/{patient}', [AdmissionController::class, 'create'])->name('admissions.create');
            Route::post('request-admission/{patient}', [AdmissionController::class, 'requestPatientAdmission'])->name('request-admission');
            Route::post('/patients/{admission}/discharge', [AdmissionController::class, 'discharge'])
                ->name('admissions.discharge')
                ->middleware('can:discharge-admissions');

            // Patient Feedback
            Route::resource('feedback', FeedBackController::class);
        });

        /*
        | Medical Records, Consultations, Prescriptions & Certificates
        */
        Route::middleware('can:view-consultations')->group(function () {
            Route::resource('consultations', ConsultationController::class)->except('index');
            Route::get('/consultation-detail/{id}', fn ($id) => Inertia::render('Consultations/Detail', ['id' => $id]))
                ->name('consultation.detail');
            Route::get('patients/{patient}/consultations', [ConsultationController::class, 'patientProfile'])
                ->name('patients.consultations.index');
            Route::get('/medical-records', fn () => Inertia::render('MedicalRecords/index'))
                ->name('medical.records');
        });

        // Doctors creating prescriptions & certificates
        Route::middleware('can:create-consultations')->group(function () {
            // Prescriptions
            Route::resource('prescriptions', PrescriptionController::class)->except(['update', 'edit']);
            Route::post('prescriptions/{prescription}/dispense', [PrescriptionController::class, 'dispense'])
                ->name('prescriptions.dispense');

            // Medical Certificates (Cameroon specific)
            Route::prefix('certificates')->group(function () {
                Route::get('/sick-leave', [CertificateController::class, 'createSickLeave'])->name('certificates.sick-leave.create');
                Route::post('/sick-leave', [CertificateController::class, 'storeSickLeave'])->name('certificates.sick-leave.store');

                Route::get('/medical', [CertificateController::class, 'createMedical'])->name('certificates.medical.create');
                Route::post('/medical', [CertificateController::class, 'storeMedical'])->name('certificates.medical.store');

                Route::get('/death', [CertificateController::class, 'createDeath'])->name('certificates.death.create');
                Route::post('/death', [CertificateController::class, 'storeDeath'])->name('certificates.death.store');

                Route::get('/{certificate}/print', [CertificateController::class, 'print'])->name('certificates.print');
            });

            // Surgeries
            Route::resource('surgeries', SurgeryController::class);
        });

        /*
        | Appointment System
        */
        Route::middleware('can:view-appointments')->group(function () {
            Route::get('/appointments/calendar', [AppointmentController::class, 'calendar'])->name('appointments.calendar');
            Route::resource('appointments', AppointmentController::class);
        });

        /*
        | Lab & Radiology Diagnostics
        */
        Route::prefix('lab')->group(function () {
            Route::middleware('can:view-lab-tests')->group(function () {
                Route::resource('tests', LabTestDefinitionController::class);
                Route::resource('requests', LabRequestController::class);
            });

            Route::middleware('can:view-lab-results')->group(function () {
                Route::resource('results', LabResultController::class);
            });
        });

        // Radiology / Imaging
        Route::prefix('radiology')->group(function () {
            Route::middleware('can:view-radiology')->group(function () {
                Route::resource('requests', RadiologyRequestController::class)->except(['edit', 'update'])->names([
                        'index' => 'radiology.requests.index',
                        'create' => 'radiology.requests.create',
                        'store' => 'radiology.requests.store',
                        'update' => 'radiology.requests.update',
                        'destroy' => 'radiology.requests.destroy',
                        'show' => 'radiology.requests.show',
                    ]);;
                Route::get('/requests/{request}/print', [RadiologyRequestController::class, 'print'])->name('radiology.requests.print');
            });

            Route::middleware('can:validate-radiology')->group(function () {
                Route::post('/results/{result}/validate', [RadiologyResultController::class, 'validate'])->name('radiology.results.validate');
            });
        });

        /*
        | Emergency
        */
        Route::middleware('can:create-admissions')->group(function () {
            Route::resource('emergencies', EmergencyCaseController::class);
        });

        /*
        | Pharmacy & Inventory
        */
        Route::prefix('pharmacy')->group(function () {
            Route::middleware('can:view-drugs')->group(function () {
                Route::resource('drugs', MedicationController::class);
                Route::put('batches/{batch}/deplete', [MedicationController::class, 'deplete'])->name('batches.deplete');
            });

            Route::middleware('can:dispense-drugs')->group(function () {
                Route::resource('dispensation', DispensationController::class);
            });
        });

        /*
        | Infrastructure & Supplies
        */
        Route::middleware('can:manage-infrastructure')->group(function () {
            Route::resource('infrastructure', InfastructureController::class);
            Route::resource('wards', WardController::class);
            Route::resource('beds', BedController::class);
            Route::resource('bed-types', BedTypeController::class);
            Route::post('beds/{bed}/transfer', [BedController::class, 'transfer'])->name('beds.transfer');
        });

        Route::middleware('can:view-supplies')->group(function () {
            Route::resource('supplies', SupplyController::class);
            Route::put('supplies/{supply}/restock', [SupplyController::class, 'restock'])
                ->name('supplies.restock')
                ->middleware('can:manage-supplies');
        });

        Route::middleware('can:manage-departments')->group(function () {
            Route::resource('departments', DepartmentController::class);
        });

        /*
        | Finance, Cash Register, Tiers Payant & Analytics
        */
        Route::middleware('can:view-finance')->group(function () {
            Route::prefix('finance')->group(function () {
                // Standard Invoicing
                Route::get('/revenue', [RevenueAnalyticsController::class, 'index'])->name('finance.revenue');
                Route::get('/expenditures', [ExpenditureController::class, 'index'])->name('finance.expenditures');
                Route::get('/invoices', [InvoiceController::class, 'index'])->name('finance.invoices.index');
                Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('finance.invoices.create');
                Route::post('/invoices', [InvoiceController::class, 'store'])->name('finance.invoices.store');
                Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('finance.invoices.show');
                Route::patch('/invoices/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('finance.invoices.status');
                Route::post('/invoices/{invoice}/record-payment', [InvoiceController::class, 'recordPayment'])->name('invoices.record-payment');
                Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('finance.invoices.destroy');

                // Tiers Payant (Cameroon Insurance/CNPS/Mutualites)
                Route::resource('payers', PayerController::class)->only(['index', 'store', 'update']);
                Route::resource('patient-insurances', PatientInsuranceController::class);
                Route::post('invoices/{invoice}/submit-claim', [InvoiceController::class, 'submitClaim'])->name('invoices.claim');
                Route::get('claims', [ClaimController::class, 'index'])->name('finance.claims.index');
                Route::get('claims/{claim}', [ClaimController::class, 'show'])->name('finance.claims.show');
                Route::post('claims', [ClaimController::class, 'store'])->name('finance.claims.store');
                Route::patch('claims/{claim}', [ClaimController::class, 'update'])->name('finance.claims.update');

                // Services catalog
                Route::resource('services', ServiceController::class)
                    ->except(['show'])
                    ->names([
                        'index' => 'finance.services.index',
                        'create' => 'finance.services.create',
                        'store' => 'finance.services.store',
                        'edit' => 'finance.services.edit',
                        'update' => 'finance.services.update',
                        'destroy' => 'finance.services.destroy',
                    ]);

                // MINSANTE Reporting (Legally required in Cameroon)
                Route::get('/reports/rpm', [ReportController::class, 'rpm'])->name('finance.reports.rpm');
            });

            Route::prefix('api/finance')->group(function () {
                Route::post('/expenditures', [ExpenditureController::class, 'store'])->name('expenditures.store');
                Route::put('/expenditures/{expenditure}', [ExpenditureController::class, 'update'])->name('expenditures.update');
                Route::delete('/expenditures/{expenditure}', [ExpenditureController::class, 'destroy'])->name('expenditures.destroy');
            });
        });

        // Cash Register / Caisse (Needs strict permission separation from viewing finance)
        Route::middleware('can:manage-caisse')->group(function () {
            Route::get('/caisse', [CaisseController::class, 'index'])->name('caisse.index');
            Route::post('/caisse/open-shift', [CaisseController::class, 'openShift'])->name('caisse.open');
            Route::post('/caisse/close-shift', [CaisseController::class, 'closeShift'])->name('caisse.close');
            Route::post('/caisse/pay', [CaisseController::class, 'pay'])->name('caisse.pay');
            Route::get('/caisse/receipt/{payment}', [CaisseController::class, 'receipt'])->name('caisse.receipt');
        });

        /*
        | System Administration (Users, Roles, Shifts, Logs)
        */
        Route::middleware('can:manage-users')->group(function () {
            Route::resource('users', UserController::class);
            Route::resource('shifts', UserShiftController::class);
        });

        Route::middleware('can:manage-roles')->group(function () {
            Route::resource('roles', RoleController::class);
        });

        Route::middleware('can:view-audit-logs')->group(function () {
            Route::get('/audit-logs', [UserActivityController::class, 'index'])->name('audit.logs');
        });

        // Notifications (all authenticated users, permission-filtered in controller)
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/latest', [NotificationController::class, 'latest'])->name('notifications.latest');
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');

        // Tenant billing portal
        Route::get('/billing', [TenantController::class, 'billing'])->name('tenant.billing');

        // Tenant support tickets
        Route::get('/support', [SupportTicketController::class, 'tenantIndex'])->name('tenant.tickets.index');
        Route::get('/support/create', [SupportTicketController::class, 'tenantCreate'])->name('tenant.tickets.create');
        Route::post('/support', [SupportTicketController::class, 'tenantStore'])->name('tenant.tickets.store');
        Route::get('/support/{supportTicket}', [SupportTicketController::class, 'tenantShow'])->name('tenant.tickets.show');
        Route::post('/support/{supportTicket}/reply', [SupportTicketController::class, 'tenantReply'])->name('tenant.tickets.reply');
    });
});
