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
use Stancl\Tenancy\Features\UserImpersonation;
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
    | SUPPORT ACCESS (impersonation) ACCEPT
    |--------------------------------------------------------------------------
    | The super admin generates a one-time token on the central domain; the
    | browser is redirected here where the tenant context already exists.
    */
    Route::get('/impersonate/{token}', function (string $token) {
        return UserImpersonation::makeResponse($token);
    })->name('impersonate.accept')->middleware('throttle:10,1');

    /*
    |--------------------------------------------------------------------------
    | AUTHENTICATED HOSPITAL ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', 'tenant.active', 'subscription.active'])->group(function () {

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
            Route::resource('patients', PatientController::class)
                ->except(['edit', 'destroy'])
                ->middlewareFor(['store'], 'plan.limit:patients');
            Route::get('vitals/create/{patient}', [VitalController::class, 'create'])->name('vitals.create');

            Route::resource('vitals', VitalController::class)->except(['create', 'index', 'show', 'edit'])
                ->parameters(['vitals' => 'vital']);
            // Vitals are always read in the context of one patient, so the
            // list is a patient sub-resource instead of a bare index.
            Route::get('patients/{patient}/vitals', [VitalController::class, 'index'])->name('vitals.index');
            Route::resource('admissions', AdmissionController::class)
                ->only(['index', 'show', 'store'])
                ->parameters(['admissions' => 'admission']);
            Route::get('admissions/create/{patient}', [AdmissionController::class, 'create'])->name('admissions.create');
            Route::post('request-admission/{patient}', [AdmissionController::class, 'requestPatientAdmission'])->name('request-admission');
            Route::post('/patients/{admission}/discharge', [AdmissionController::class, 'discharge'])
                ->name('admissions.discharge')
                ->middleware('can:discharge-admissions');
        });

        /*
        | Medical Records, Consultations, Prescriptions & Certificates
        */
        Route::middleware('can:view-consultations')->group(function () {
            Route::resource('consultations', ConsultationController::class)->only(['create', 'store', 'show']);
            Route::get('patients/{patient}/consultations', [ConsultationController::class, 'patientProfile'])
                ->name('patients.consultations.index');
        });

        // Doctors creating prescriptions & certificates
        Route::middleware('can:create-consultations')->group(function () {
            // Prescriptions
            Route::resource('prescriptions', PrescriptionController::class)->except(['update', 'edit']);
            Route::post('prescriptions/{prescription}/dispense', [PrescriptionController::class, 'dispense'])
                ->name('prescriptions.dispense');

            // Medical Certificates (Cameroon specific) — one entry point, the
            // certificate type is chosen inside the form.
            Route::prefix('certificates')->group(function () {
                Route::get('/', [CertificateController::class, 'create'])->name('certificates.create');
                Route::post('/', [CertificateController::class, 'store'])->name('certificates.store');

                Route::get('/{certificate}/print', [CertificateController::class, 'print'])->name('certificates.print');
            });

            // Surgeries
            Route::resource('surgeries', SurgeryController::class);
        });

        /*
        | Appointment System
        */
        Route::middleware(['can:view-appointments', 'feature:appointments'])->group(function () {
            Route::get('/appointments/calendar', [AppointmentController::class, 'calendar'])->name('appointments.calendar');
            Route::resource('appointments', AppointmentController::class)->only(['index', 'create', 'store', 'update']);
        });

        /*
        | Lab & Radiology Diagnostics
        */
        Route::prefix('lab')->middleware('feature:laboratory')->group(function () {
            Route::middleware('can:view-lab-tests')->group(function () {
                Route::resource('tests', LabTestDefinitionController::class)->except(['show', 'edit']);
                Route::resource('requests', LabRequestController::class)->only(['index']);
            });

            Route::middleware('can:view-lab-results')->group(function () {
                Route::resource('results', LabResultController::class)->except(['show']);
            });
        });

        // Radiology / Imaging
        Route::prefix('radiology')->middleware('feature:radiology')->group(function () {
            Route::middleware('can:view-radiology')->group(function () {
                Route::resource('requests', RadiologyRequestController::class)->except(['edit', 'update'])->names([
                    'index' => 'radiology.requests.index',
                    'create' => 'radiology.requests.create',
                    'store' => 'radiology.requests.store',
                    'update' => 'radiology.requests.update',
                    'destroy' => 'radiology.requests.destroy',
                    'show' => 'radiology.requests.show',
                ]);
                Route::get('/requests/{request}/print', [RadiologyRequestController::class, 'print'])->name('radiology.requests.print');

                Route::resource('results', RadiologyResultController::class)->except(['show', 'destroy'])->names([
                    'index' => 'radiology.results.index',
                    'create' => 'radiology.results.create',
                    'store' => 'radiology.results.store',
                    'edit' => 'radiology.results.edit',
                    'update' => 'radiology.results.update',
                ]);
            });

            Route::middleware('can:validate-radiology')->group(function () {
                Route::post('/results/{result}/validate', [RadiologyResultController::class, 'validate'])->name('radiology.results.validate');
            });
        });

        /*
        | Emergency
        */
        Route::middleware(['can:create-admissions', 'feature:emergency'])->group(function () {
            Route::resource('emergencies', EmergencyCaseController::class)->except(['edit']);
        });

        /*
        | Pharmacy & Inventory
        */
        Route::prefix('pharmacy')->middleware('feature:pharmacy')->group(function () {
            Route::middleware('can:view-drugs')->group(function () {
                Route::resource('drugs', MedicationController::class)->except(['show', 'edit']);
                Route::put('batches/{batch}/deplete', [MedicationController::class, 'deplete'])->name('batches.deplete');
            });

            Route::middleware('can:dispense-drugs')->group(function () {
                Route::resource('dispensation', DispensationController::class)->only(['index', 'store']);
            });
        });

        /*
        | Infrastructure & Supplies
        */
        Route::middleware(['can:manage-infrastructure', 'feature:admissions'])->group(function () {
            Route::resource('wards', WardController::class)->only(['index', 'store', 'update', 'destroy']);
            Route::resource('beds', BedController::class)->only(['index', 'store', 'update', 'destroy']);
            Route::resource('bed-types', BedTypeController::class)->only(['index', 'store', 'update', 'destroy']);
            Route::post('beds/{bed}/transfer', [BedController::class, 'transfer'])->name('beds.transfer');
        });

        Route::middleware(['can:view-supplies', 'feature:inventory'])->group(function () {
            Route::resource('supplies', SupplyController::class)->except(['show']);
            Route::put('supplies/{supply}/restock', [SupplyController::class, 'restock'])
                ->name('supplies.restock')
                ->middleware('can:manage-supplies');
        });

        Route::middleware('can:manage-departments')->group(function () {
            Route::resource('departments', DepartmentController::class)->only(['index', 'store', 'update', 'destroy']);
        });

        /*
        | Finance, Cash Register, Tiers Payant & Analytics
        */
        Route::middleware('can:view-finance')->group(function () {
            Route::prefix('finance')->group(function () {
                // Standard Invoicing
                Route::get('/revenue', [RevenueAnalyticsController::class, 'index'])
                    ->middleware('feature:reports')
                    ->name('finance.revenue');
                Route::get('/expenditures', [ExpenditureController::class, 'index'])->name('finance.expenditures');
                Route::get('/invoices', [InvoiceController::class, 'index'])->name('finance.invoices.index');
                Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('finance.invoices.create');
                Route::post('/invoices', [InvoiceController::class, 'store'])->name('finance.invoices.store');
                Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('finance.invoices.show');
                Route::patch('/invoices/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('finance.invoices.status');
                Route::post('/invoices/{invoice}/record-payment', [InvoiceController::class, 'recordPayment'])->name('invoices.record-payment');
                Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('finance.invoices.destroy');

                // Tiers Payant (Cameroon Insurance/CNPS/Mutualites)
                Route::middleware('feature:insurance_claims')->group(function () {
                    Route::resource('payers', PayerController::class)->only(['index', 'store', 'update']);
                    Route::resource('patient-insurances', PatientInsuranceController::class)->except(['show']);
                    Route::post('invoices/{invoice}/submit-claim', [InvoiceController::class, 'submitClaim'])->name('invoices.claim');
                    Route::get('claims', [ClaimController::class, 'index'])->name('finance.claims.index');
                    Route::get('claims/{claim}', [ClaimController::class, 'show'])->name('finance.claims.show');
                    Route::post('claims', [ClaimController::class, 'store'])->name('finance.claims.store');
                    Route::patch('claims/{claim}', [ClaimController::class, 'update'])->name('finance.claims.update');
                });

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
                Route::middleware('feature:reports')->group(function () {
                    Route::get('/reports/rpm', [ReportController::class, 'rpm'])->name('finance.reports.rpm');
                });
            });

            Route::prefix('api/finance')->group(function () {
                Route::post('/expenditures', [ExpenditureController::class, 'store'])->name('expenditures.store');
                Route::put('/expenditures/{expenditure}', [ExpenditureController::class, 'update'])->name('expenditures.update');
                Route::delete('/expenditures/{expenditure}', [ExpenditureController::class, 'destroy'])->name('expenditures.destroy');
            });
        });

        // Cash Register / Caisse (Needs strict permission separation from viewing finance)
        Route::middleware(['can:manage-caisse', 'feature:cash_register'])->group(function () {
            Route::get('/caisse', [CaisseController::class, 'index'])->name('caisse.index');
            Route::get('/caisse/patients', [CaisseController::class, 'searchPatients'])->name('caisse.patients');
            Route::get('/caisse/outstanding/{patient}', [CaisseController::class, 'outstanding'])->name('caisse.outstanding');
            Route::post('/caisse/settle', [CaisseController::class, 'settle'])->name('caisse.settle');
            Route::post('/caisse/open-shift', [CaisseController::class, 'openShift'])->name('caisse.open');
            Route::post('/caisse/close-shift', [CaisseController::class, 'closeShift'])->name('caisse.close');
            Route::post('/caisse/pay', [CaisseController::class, 'pay'])->name('caisse.pay');
            Route::get('/caisse/receipt/{payment}', [CaisseController::class, 'receipt'])->name('caisse.receipt');
        });

        /*
        | System Administration (Users, Roles, Shifts, Logs)
        */
        Route::middleware('can:manage-users')->group(function () {
            Route::resource('users', UserController::class)
                ->except(['show', 'edit'])
                ->middlewareFor(['store'], 'plan.limit:users');
            Route::get('/users/{user}/pricing', [UserController::class, 'pricing'])->name('users.pricing');
            Route::put('/users/{user}/pricing', [UserController::class, 'updatePricing'])->name('users.pricing.update');
            Route::resource('shifts', UserShiftController::class)->only(['index', 'store', 'update', 'destroy']);
        });

        Route::middleware('can:manage-roles')->group(function () {
            Route::resource('roles', RoleController::class)->except(['create', 'show', 'edit']);
        });

        Route::middleware(['can:view-audit-logs', 'feature:audit_logs'])->group(function () {
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
