<?php

use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BedController;
use App\Http\Controllers\BedTypeController;
use App\Http\Controllers\ConsultationController;
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
use App\Http\Controllers\PatientController;
use App\Http\Controllers\RevenueAnalyticsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SupplyController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\SurgeryController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\UserActivityController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserShiftController;
use App\Http\Controllers\VitalController;
use App\Http\Controllers\WardController;
use App\Services\System\DashboardService;
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
        | Dashboards (Role-Based Views)
        */
        Route::prefix('dashboard')->group(function () {
            Route::get('/admin', function () {
                $dashboard = app(DashboardService::class);

                return Inertia::render('Dashboard/Admin', [
                    'metrics' => $dashboard->getAllMetrics(),
                ]);
            })
                ->name('dashboard.admin')
                ->middleware('can:view-finance');

            Route::get('/doctor', fn () => Inertia::render('Dashboard/Doctor'))
                ->name('dashboard.doctor')
                ->middleware('can:view-consultations');

            Route::get('/reception', fn () => Inertia::render('Dashboard/Reception'))
                ->name('dashboard.reception')
                ->middleware('can:view-appointments');

            Route::get('/lab', fn () => Inertia::render('Dashboard/LabTech'))
                ->name('dashboard.lab')
                ->middleware('can:view-lab-tests');

            Route::get('/pharma', fn () => Inertia::render('Dashboard/Pharma'))
                ->name('dashboard.pharma')
                ->middleware('can:view-drugs');
        });

        /*
        | Patient Management & Admissions
        */
        Route::middleware('can:view-patients')->group(function () {
            Route::resource('patients', PatientController::class);
            Route::resource('vitals', VitalController::class);
            Route::resource('admissions', AdmissionController::class)->except('create');
            Route::get('admissions/create/{patient}', [AdmissionController::class, 'create'])->name('admissions.create');
            Route::post('request-admission/{patient}', [AdmissionController::class, 'requestPatientAdmission'])->name('request-admission');
            Route::post('/patients/{admission}/discharge', [AdmissionController::class, 'discharge'])
                ->name('admissions.discharge')
                ->middleware('can:discharge-admissions');
        });

        /*
        | Medical Records & Consultations
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

        /*
        | Surgery & Emergency
        */
        Route::middleware('can:create-consultations')->group(function () {
            Route::resource('surgeries', SurgeryController::class);
        });

        Route::middleware('can:create-admissions')->group(function () {
            Route::resource('emergencies', EmergencyCaseController::class);
        });

        /*
        | Appointment System
        */
        Route::middleware('can:view-appointments')->group(function () {
            Route::get('/appointments-book', fn () => Inertia::render('Appointments/Book'))->name('appointments.book');
            Route::resource('appointments', AppointmentController::class);
        });

        /*
        | Lab & Diagnostics
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

        /*
        | Pharmacy & Inventory
        */
        Route::prefix('pharmacy')->group(function () {
            Route::middleware('can:view-drugs')->group(function () {
                Route::resource('drugs', MedicationController::class);
                Route::put('batches/{batch}/deplete', [MedicationController::class, 'deplete'])
                    ->name('batches.deplete');
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
        | Finance, Analytics & Feedback
        */
        Route::middleware('can:view-finance')->group(function () {
            Route::prefix('finance')->group(function () {
                Route::get('/revenue', [RevenueAnalyticsController::class, 'index'])->name('finance.revenue');
                Route::get('/expenditures', [ExpenditureController::class, 'index'])->name('finance.expenditures');
                Route::get('/invoices', [InvoiceController::class, 'index'])->name('finance.invoices.index');
                Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('finance.invoices.create');
                Route::post('/invoices', [InvoiceController::class, 'store'])->name('finance.invoices.store');
                Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('finance.invoices.show');
                Route::patch('/invoices/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('finance.invoices.status');
                Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('finance.invoices.destroy');
            });
            Route::prefix('api/finance')->group(function () {
                Route::post('/expenditures', [ExpenditureController::class, 'store'])->name('expenditures.store');
                Route::put('/expenditures/{expenditure}', [ExpenditureController::class, 'update'])->name('expenditures.update');
                Route::delete('/expenditures/{expenditure}', [ExpenditureController::class, 'destroy'])->name('expenditures.destroy');
            });
        });

        Route::middleware('can:view-patients')->group(function () {
            Route::resource('feedback', FeedBackController::class);
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
