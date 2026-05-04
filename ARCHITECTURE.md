# 🏗️ SanaGo Architecture Documentation

This document provides an in-depth technical overview of the SanaGo Hospital Management System architecture.

## Table of Contents

- [System Overview](#system-overview)
- [Multi-Tenancy Architecture](#multi-tenancy-architecture)
- [Application Layers](#application-layers)
- [Database Design](#database-design)
- [Security Architecture](#security-architecture)
- [Notification System](#notification-system)
- [API Architecture](#api-architecture)
- [Performance Optimization](#performance-optimization)
- [Scalability](#scalability)

---

## System Overview

SanaGo is built on a modern, layered architecture designed for:
- **Scalability**: Horizontal scaling across multiple servers
- **Security**: Multi-layered security with encryption and isolation
- **Performance**: Sub-100ms response times with Octane
- **Maintainability**: Clean separation of concerns
- **Real-time Capabilities**: WebSocket-based notifications via Laravel Reverb
- **Multi-Tenancy**: Single-database architecture with complete tenant isolation

### Technology Stack

| Category | Technology | Version | Purpose |
|----------|-----------|---------|---------|
| **Framework** | Laravel | 12.x | Core application framework |
| **Frontend** | Livewire | 3.x | Full-stack reactive UI components |
| **Server** | FrankenPHP + Octane | Latest | High-performance PHP server |
| **Database** | PostgreSQL | 15+ | Primary relational database |
| **Cache** | Redis | 7+ | In-memory caching and pub/sub |
| **Storage** | MinIO (S3) | Latest | Object storage for files |
| **Multi-Tenancy** | Stancl Tenancy | 3.9.1 | Tenant management and isolation |
| **Real-time** | Laravel Reverb | 1.0+ | WebSocket server for notifications |
| **Encryption** | Spatie CipherSweet | 1.7 | Field-level data encryption |
| **Authentication** | Laravel Sanctum | 4.0 | API token authentication |

---

## Multi-Tenancy Architecture

### Single-Database Multi-Tenant Model

All tenants share a single PostgreSQL database with complete data isolation via tenant_id scoping:

```
Single Database (sanago_central)
├── Central Tables (no tenant_id)
│   ├── tenants (id, name, created_at, data)
│   ├── domains (id, domain, tenant_id)
│   └── subscriptions (id, tenant_id, plan, status)
│
└── Tenant-Scoped Tables (with tenant_id column)
    ├── users (tenant_id, name, email, role, ...)
    ├── patients (tenant_id, first_name, last_name, ...)
    ├── appointments (tenant_id, patient_id, doctor_id, ...)
    ├── medical_records (tenant_id, patient_id, diagnosis, ...)
    ├── prescriptions (tenant_id, patient_id, doctor_id, ...)
    ├── lab_requests (tenant_id, patient_id, test_type, ...)
    ├── medications (tenant_id, name, stock_quantity, ...)
    ├── lab_results (tenant_id, lab_request_id, results, ...)
    ├── admissions (tenant_id, patient_id, bed_id, ...)
    ├── invoices (tenant_id, patient_id, total_amount, ...)
    └── ... (30+ tables with automatic tenant_id filtering)
```

### Tenant Identification Flow

```php
// 1. Request arrives: hospital-a.sanago.com
// 2. Middleware extracts subdomain: "hospital-a"
// 3. Query for tenant
$tenant = Tenant::whereHas('domains', function($q) {
    $q->where('domain', 'hospital-a.sanago.com');
})->first();

// 4. Initialize tenancy (sets tenant context)
tenancy()->initialize($tenant);

// 5. All subsequent queries automatically scoped by tenant_id
$patients = Patient::all(); // Automatically adds WHERE tenant_id = 'abc123'
```

### Tenant Isolation Layers

1. **Query Isolation**: Global scopes automatically filter all queries by tenant_id
2. **File Isolation**: S3 prefixes per tenant (`tenant_<id>/uploads/...`)
3. **Cache Isolation**: Redis key prefixes (`tenant:<id>:cache:...`)
4. **Queue Isolation**: Job metadata includes tenant context
5. **Session Isolation**: Tenant-scoped session storage
6. **Index Optimization**: Composite indexes on (tenant_id, ...) for fast queries

---

## Application Layers

### 1. Presentation Layer (Livewire Components & API Controllers)

**Livewire Components** (Primary UI):
```
app/Livewire/
├── Tenants/
│   ├── Admin/               # Hospital administration
│   │   ├── Index.php        # Dashboard
│   │   ├── Settings.php     # System settings
│   │   ├── UserManagement.php
│   │   ├── Shifts.php
│   │   └── Components/
│   ├── Doctor/              # Doctor-specific features
│   │   ├── Index.php        # Dashboard
│   │   ├── DoctorAppointment.php
│   │   ├── MedicalRecord.php
│   │   ├── Patient.php
│   │   └── Components/
│   ├── Nurse/               # Nursing features
│   │   ├── Dashboard.php
│   │   ├── RecordVitals.php
│   │   ├── CreateCareReport.php
│   │   └── Components/
│   ├── LabTechnician/       # Laboratory operations
│   │   ├── Index.php        # Dashboard
│   │   ├── TestRequest.php
│   │   ├── EnterResults.php
│   │   ├── ManageLabTestDefinitions.php
│   │   └── Components/
│   ├── Pharmacist/          # Pharmacy management
│   │   ├── Dashboard.php
│   │   ├── Medications.php
│   │   ├── ManageDrugsInventory.php
│   │   └── Components/
│   └── Receptionist/        # Front desk operations
│       ├── Index.php        # Dashboard
│       ├── Patients.php
│       ├── Appointments.php
│       ├── AdmitPatient.php
│       └── Components/
└── LandLord/                # System-wide management
    ├── Dashboard.php
    ├── ManageTenants.php
    ├── ManageSubscription.php
    └── Components/
```

**API Controllers** (RESTful endpoints):
```
app/Http/Controllers/Api/
├── Tenants/
│   ├── NotificationController.php  # Notification management
│   ├── Admin/                     # Admin endpoints
│   │   ├── AdminDashboardController.php
│   │   ├── AdminUserController.php
│   │   ├── AdminShiftController.php
│   │   ├── AdminRevenueController.php
│   │   └── AdminSettingsController.php
│   ├── Doctor/                     # Doctor endpoints
│   │   ├── DoctorDashboardController.php
│   │   ├── AppointmentController.php
│   │   ├── DoctorPatientController.php
│   │   ├── LabRequestController.php
│   │   └── MedicalRecordApiController.php
│   ├── LabTechnician/             # Lab technician endpoints
│   │   ├── LabTechnicianDashboardController.php
│   │   └── LabTechnicianController.php
│   ├── Nurse/                     # Nurse endpoints
│   │   └── NurseDashboardController.php
│   ├── Pharmacist/                # Pharmacist endpoints
│   │   ├── PharmacistDashboardController.php
│   │   └── PharmacistController.php
│   └── Receptionist/              # Receptionist endpoints
│       ├── ReceptionistDashboardController.php
│       ├── PatientController.php
│       ├── AppointmentController.php
│       └── AdmissionController.php
```

### 2. Business Logic Layer (Services)

**Core Services**:
```php
app/Services/
├── NotificationService.php              # Notification management
├── AppointmentService.php               # Appointment operations
├── AdmissionService.php                 # Patient admissions
├── BillingService.php                   # Invoice generation
├── LabService.php                       # Lab operations
├── MedicalRecordService.php             # Medical records
├── AdminShiftService.php                # Shift management
└── Dashboards/                          # Role-specific dashboards
    ├── AdminDashboardService.php
    ├── DoctorDashboardService.php
    ├── NurseDashboardService.php
    ├── LabTechnicianDashboardService.php
    ├── PharmacistDashboardService.php
    └── ReceptionistDashboardService.php
```

**Example Service**:
```php
class NotificationService
{
    public function sendNewLabOrderNotification(MedicalRecord $medicalRecord)
    {
        $labTechnicians = User::where('role', 'lab_technician')->get();
        
        foreach ($labTechnicians as $technician) {
            $technician->notify(new NewLabOrderNotification($medicalRecord));
        }
    }
    
    public function sendNewPrescriptionNotification(Prescription $prescription)
    {
        $pharmacists = User::where('role', 'pharmacist')->get();
        
        foreach ($pharmacists as $pharmacist) {
            $pharmacist->notify(new NewPrescriptionOrder($prescription));
        }
    }
}
```

### 3. Data Access Layer (Models & Resources)

**Core Models**:
```php
app/Models/
├── User.php                          # User accounts
├── Patient.php                       # Patient records
├── Appointment.php                   # Appointments
├── MedicalRecord.php                 # Medical records
├── Prescription.php                  # Prescriptions
├── PrescriptionItem.php              # Prescription items
├── LabRequest.php                    # Lab requests
├── LabResult.php                     # Lab results
├── LabTestDefinition.php             # Lab test definitions
├── Medication.php                    # Medications
├── Dispensation.php                  # Medication dispensing
├── Admission.php                     # Patient admissions
├── Vital.php                         # Vital signs
├── NurseCareReport.php               # Nursing care reports
├── Department.php                    # Departments
├── Ward.php                          # Wards
├── Bed.php                           # Beds
├── Supply.php                        # Supplies
├── SupplyUsage.php                   # Supply usage
├── Invoice.php                       # Invoices
├── RevenueSummary.php                # Revenue tracking
├── UserActivity.php                  # Audit logging
├── UserShift.php                     # Staff shifts
└── Notification.php                  # User notifications
```

**API Resources**:
```php
app/Http/Resources/
├── NotificationResource.php
├── PatientResource.php
├── AppointmentResource.php
├── MedicalRecordResource.php
├── AdmissionResource.php
├── LabRequestResource.php
├── DepartmentResource.php
└── UserResource.php
```

**Model Relationships**:
```php
class User extends Authenticatable implements MustVerifyEmail
{
    use BelongsToTenant, HasApiTokens, HasFactory, Notifiable;
    
    protected $fillable = [
        'name', 'email', 'password', 'role', 'department_id', 'tenant_id'
    ];
    
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }
    
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class, 'doctor_id');
    }
    
    public function labRequests()
    {
        return $this->hasMany(LabRequest::class, 'requested_by_doctor_id');
    }
    
    public function labResults()
    {
        return $this->hasMany(LabResult::class, 'lab_technician_id');
    }
    
    public function dispensations()
    {
        return $this->hasMany(Dispensation::class, 'pharmacist_id');
    }
}
```

---

## Notification System

### Architecture Overview

SanaGo implements a comprehensive notification system with support for both database and real-time notifications:

```
┌─────────────────────────────────────────────────────────────┐
│                     Notification System                     │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│ ┌──────────────────┐                                        │
│ │  Notification    │                                        │
│ │  Service         │                                        │
│ └──────┬───────────┘                                        │
│        │                                                    │
│        ├──────────────────────────────────────────────────┐ │
│        │                                                  │ │
│        ▼                                                  ▼ │
│ ┌──────────────┐                          ┌──────────────┐ │
│ │  Database    │                          │  Broadcast   │ │
│ │  Notifications │                        │  (Reverb)    │ │
│ └──────────────┘                          └──────────────┘ │
│        │                                                  │
│        └──────────────────────────────────────────────────┘
│                                                              │
│ ┌─────────────────────────────────────────────────────────┐
│ │  Notification Types (app/Notifications/)                 │
│ │                                                           │
│ │ ├─ NewLabOrderNotification.php       (Lab Technicians)  │
│ │ ├─ NewPrescriptionOrder.php          (Pharmacists)      │
│ │ ├─ NewPatientAdmissionNotification.php (Nurses)        │
│ │ ├─ AppointmentReminderNotification.php (Doctors)       │
│ │ └─ LabResultNotification.php         (Doctors)         │
│ └─────────────────────────────────────────────────────────┘
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### Notification Types

#### 1. New Lab Order Notification
```php
class NewLabOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;
    
    public $medicalRecord;
    
    public function __construct(MedicalRecord $medicalRecord)
    {
        $this->medicalRecord = $medicalRecord->load('patient', 'doctor');
    }
    
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }
    
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'data' => $this->getData(),
            'read_at' => null,
            'created_at' => now()->toIso8601String(),
        ]);
    }
    
    public function toDatabase(object $notifiable): array
    {
        return $this->getData();
    }
    
    private function getData(): array
    {
        return [
            'id' => $this->id,
            'message' => 'New Lab Request(s) Available',
            'patient_name' => $this->medicalRecord->patient->first_name . ' ' . 
                             $this->medicalRecord->patient->last_name,
            'doctor_name' => $this->medicalRecord->doctor->name ?? 'Unknown Doctor',
            'consultation_id' => $this->medicalRecord->id,
            'type' => 'lab_order',
            'urgency' => 'normal',
            'created_at' => now()->toIso8601String(),
        ];
    }
    
    public function broadcastOn(): array
    {
        return [new PrivateChannel('lab.requests')];
    }
}
```

#### 2. Notification Service
```php
class NotificationService
{
    public function sendNewLabOrderNotification(MedicalRecord $medicalRecord)
    {
        $labTechnicians = User::where('role', 'lab_technician')->get();
        
        foreach ($labTechnicians as $technician) {
            $technician->notify(new NewLabOrderNotification($medicalRecord));
        }
    }
    
    public function sendNewPrescriptionNotification(Prescription $prescription)
    {
        $pharmacists = User::where('role', 'pharmacist')->get();
        
        foreach ($pharmacists as $pharmacist) {
            $pharmacist->notify(new NewPrescriptionOrder($prescription));
        }
    }
    
    public function sendNewPatientAdmissionNotification(Admission $admission)
    {
        $nurses = User::where('role', 'nurse')->get();
        
        foreach ($nurses as $nurse) {
            $nurse->notify(new NewPatientAdmissionNotification($admission));
        }
    }
    
    public function sendAppointmentReminderNotification(Appointment $appointment)
    {
        $doctor = User::find($appointment->doctor_id);
        
        if ($doctor) {
            $doctor->notify(new AppointmentReminderNotification($appointment));
        }
    }
}
```

---

## API Architecture

### Route Structure

```php
// routes/api.php
Route::prefix('api')->group(function () {
    // Public routes
    Route::get('/ping', fn () => response()->noContent());
    Route::post('/login', [LoginController::class, 'login']);
    
    // Protected routes (Authenticated & Tenant Verified)
    Route::middleware(['auth:sanctum', 'tenant.auth'])->group(function () {
        Route::post('/logout', [LoginController::class, 'logout']);
        Route::get('/user', fn (Request $request) => $request->user());
        
        // Admin routes
        Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
            Route::get('/dashboard', [AdminDashboardController::class, 'index']);
            Route::apiResource('users', AdminUserController::class);
            Route::apiResource('shifts', AdminShiftController::class);
            Route::get('/revenue', [AdminRevenueController::class, 'index']);
            Route::prefix('settings')->group(function () {
                Route::get('/options', [AdminSettingsController::class, 'options']);
                Route::get('/{type}', [AdminSettingsController::class, 'index']);
                Route::post('/{type}', [AdminSettingsController::class, 'store']);
                Route::put('/{type}/{id}', [AdminSettingsController::class, 'update']);
                Route::delete('/{type}/{id}', [AdminSettingsController::class, 'destroy']);
            });
        });
        
        // Doctor routes
        Route::middleware('role:doctor')->prefix('doctor')->name('doctor.')->group(function () {
            Route::get('/dashboard', [DoctorDashboardController::class, 'index']);
            Route::controller(DoctorAppointmentController::class)->prefix('appointments')->group(function () {
                Route::get('/', 'index');
                Route::patch('/{appointment}/start', 'start');
                Route::patch('/{appointment}/end', 'end');
            });
            Route::controller(MedicalRecordApiController::class)->group(function () {
                Route::get('/consultations/{id}', 'showConsultation');
                Route::get('/patients/{patient}', 'show');
                Route::prefix('medical-records')->group(function () {
                    Route::post('/', 'store');
                    Route::get('/context/{patientId}', 'getConsultationContext');
                });
            });
        });
        
        // Lab Technician routes
        Route::middleware('role:lab-technician')->prefix('lab-technician')->name('lab-technician.')->group(function () {
            Route::get('/dashboard', [LabTechnicianDashboardController::class, 'index']);
            Route::get('/lab-requests', [LabTechnicianController::class, 'getLabRequests']);
            Route::patch('/lab-requests/{labRequest}/start', [LabTechnicianController::class, 'startRequest']);
            Route::post('/lab-requests/{labRequest}/results', [LabTechnicianController::class, 'submitResults']);
            Route::apiResource('test-definitions', LabTechnicianController::class);
        });
        
        // Pharmacist routes
        Route::middleware('role:pharmacist')->prefix('pharmacist')->name('pharmacist.')->group(function () {
            Route::get('/dashboard', [PharmacistDashboardController::class, 'index']);
            Route::get('/medications', [PharmacistDashboardController::class, 'medications']);
            Route::apiResource('inventory', PharmacistController::class);
            Route::get('/patients', [PharmacistController::class, 'getPatients']);
            Route::get('/patients/{patientId}/prescriptions', [PharmacistController::class, 'getPatientPrescriptions']);
            Route::post('/prescriptions/{prescriptionId}/dispense', [PharmacistController::class, 'dispenseItems']);
        });
        
        // Shared routes
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::get('/unread', [NotificationController::class, 'unread']);
            Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
            Route::get('/{id}', [NotificationController::class, 'show']);
            Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
            Route::post('/{id}/mark-read', [NotificationController::class, 'markAsRead']);
            Route::delete('/{id}', [NotificationController::class, 'destroy']);
        });
    });
});
```

### API Resource Example

```php
class NotificationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'data' => $this->data,
            'read_at' => $this->read_at,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
```

---

## Database Design

### Entity Relationship Diagram (Simplified)

```
┌─────────────┐       ┌──────────────┐       ┌─────────────────┐
│   Patient   │──────<│ Appointment  │>──────│     Doctor      │
│             │       │              │       │    (User)       │
│ - id        │       │ - id         │       │                 │
│ - name      │       │ - patient_id │       │ - id            │
│ - dob       │       │ - doctor_id  │       │ - name          │
│ - phone     │       │ - date       │       │ - role          │
└─────┬───────┘       │ - status     │       └─────────────────┘
      │               └──────┬───────┘
      │                      │
      │                      │
      ▼                      ▼
┌─────────────────┐   ┌──────────────┐
│ MedicalRecord   │   │ Prescription │
│                 │   │              │
│ - id            │   │ - id         │
│ - patient_id    │   │ - appt_id    │
│ - diagnosis     │   │ - doctor_id  │
│ - notes         │   └──────┬───────┘
└─────────────────┘          │
                             ▼
                      ┌──────────────────┐
                      │ PrescriptionItem │
                      │                  │
                      │ - id             │
                      │ - prescription_id│
                      │ - medication_id  │
                      │ - dosage         │
                      └──────────────────┘
```

### Key Tables

#### Patients Table
```sql
CREATE TABLE patients (
    id BIGSERIAL PRIMARY KEY,
    tenant_id VARCHAR(255) NOT NULL,  -- Required for multi-tenancy
    first_name VARCHAR(255),
    last_name VARCHAR(255),
    date_of_birth DATE,
    gender VARCHAR(50),
    phone VARCHAR(20),
    email VARCHAR(255),
    address TEXT,
    emergency_contact JSONB,
    medical_history JSONB,
    allergies JSONB,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    -- Composite index for tenant-scoped queries
    INDEX idx_tenant_patients (tenant_id, id),
    INDEX idx_tenant_name (tenant_id, last_name, first_name)
);
```

#### Appointments Table
```sql
CREATE TABLE appointments (
    id BIGSERIAL PRIMARY KEY,
    tenant_id VARCHAR(255) NOT NULL,  -- Required for multi-tenancy
    patient_id BIGINT REFERENCES patients(id),
    doctor_id BIGINT REFERENCES users(id),
    appointment_date TIMESTAMP,
    duration INTEGER DEFAULT 30,
    reason TEXT,
    status VARCHAR(50) DEFAULT 'scheduled',
    notes TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    -- Composite indexes for tenant-scoped queries
    INDEX idx_tenant_doctor_date (tenant_id, doctor_id, appointment_date),
    INDEX idx_tenant_patient (tenant_id, patient_id),
    INDEX idx_tenant_status (tenant_id, status)
);
```

#### Medical Records Table
```sql
CREATE TABLE medical_records (
    id BIGSERIAL PRIMARY KEY,
    tenant_id VARCHAR(255) NOT NULL,  -- Required for multi-tenancy
    patient_id BIGINT REFERENCES patients(id),
    doctor_id BIGINT REFERENCES users(id),
    appointment_id BIGINT REFERENCES appointments(id),
    chief_complaint TEXT,
    history_present_illness TEXT,
    physical_examination TEXT,
    diagnosis TEXT,
    treatment_plan TEXT,
    follow_up_date DATE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    -- Composite indexes for tenant-scoped queries
    INDEX idx_tenant_patient (tenant_id, patient_id),
    INDEX idx_tenant_doctor (tenant_id, doctor_id)
);
```

### Indexing Strategy

```sql
-- Tenant-scoped composite indexes (CRITICAL for performance)
CREATE INDEX idx_patients_tenant ON patients(tenant_id, id);
CREATE INDEX idx_appointments_tenant_doctor_date ON appointments(tenant_id, doctor_id, appointment_date);
CREATE INDEX idx_appointments_tenant_patient ON appointments(tenant_id, patient_id);
CREATE INDEX idx_lab_requests_tenant_status ON lab_requests(tenant_id, status);

-- Full-text search (tenant-scoped)
CREATE INDEX idx_patients_search ON patients USING GIN(tenant_id, to_tsvector('english', first_name || ' ' || last_name));

-- Ensure data integrity
ALTER TABLE patients ADD CONSTRAINT fk_patients_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE;
ALTER TABLE appointments ADD CONSTRAINT fk_appointments_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE;
```

---

## Security Architecture

### 1. Authentication Flow

```
User Login Request
      │
      ▼
┌─────────────────┐
│ Login Component │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Validate Creds  │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Check 2FA       │ ◄── If enabled
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Create Session  │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Log Activity    │
└────────┬────────┘
         │
         ▼
    Redirect to Dashboard
```

### 2. Authorization (RBAC)

```php
// Middleware checks
Route::middleware(['auth', 'role:doctor'])->group(function() {
    Route::get('/patients', [DoctorController::class, 'patients']);
});

// In Livewire components
public function mount()
{
    $this->authorize('view', Patient::class);
}

// In Blade views
@can('create', App\Models\Prescription::class)
    <button>Create Prescription</button>
@endcan
```

### 3. Data Encryption

**Field-Level Encryption** (CipherSweet):
```php
use Spatie\LaravelCipherSweet\Concerns\UsesCipherSweet;

class Patient extends Model
{
    use UsesCipherSweet;
    
    protected $cipherSweetEncrypted = [
        'ssn',           // Social Security Number
        'insurance_id',  // Insurance ID
    ];
    
    protected $cipherSweetBlindIndexes = [
        'ssn' => [
            'type' => 'last_four',
            'length' => 4,
        ],
    ];
}

// Usage
$patient->ssn = '123-45-6789';  // Automatically encrypted
$patient->save();

// Search by encrypted field
Patient::whereBlindIndex('ssn', 'last_four', '6789')->get();
```

### 4. Audit Logging

```php
class UserActivity extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'changes',
        'ip_address',
        'user_agent',
    ];
}

// Automatic logging via model events
Patient::observe(PatientObserver::class);

class PatientObserver
{
    public function updated(Patient $patient)
    {
        UserActivity::create([
            'user_id' => auth()->id(),
            'action' => 'updated',
            'model_type' => Patient::class,
            'model_id' => $patient->id,
            'changes' => $patient->getChanges(),
            'ip_address' => request()->ip(),
        ]);
    }
}
```

---

## Performance Optimization

### 1. Laravel Octane

**Traditional PHP-FPM**:
```
Request → Bootstrap Laravel → Process → Shutdown
(~50-100ms per request)
```

**Laravel Octane**:
```
Bootstrap Laravel (once)
    ↓
Request → Process → Response
(~10-20ms per request)
```

**Configuration** (`config/octane.php`):
```php
return [
    'server' => 'frankenphp',
    'workers' => 4,
    'max_requests' => 1000,
    'warm' => [
        'config',
        'routes',
        'views',
    ],
];
```

### 2. Database Query Optimization

**N+1 Query Problem**:
```php
// ❌ Bad: N+1 queries
$appointments = Appointment::all();
foreach ($appointments as $appointment) {
    echo $appointment->patient->name;  // Query per iteration
}

// ✅ Good: Eager loading
$appointments = Appointment::with('patient')->get();
foreach ($appointments as $appointment) {
    echo $appointment->patient->name;  // No additional queries
}
```

**Query Caching**:
```php
$patients = Cache::remember('active_patients', 3600, function() {
    return Patient::where('is_active', true)->get();
});
```

### 3. Redis Caching Strategy

```php
// Cache frequently accessed data
Cache::tags(['patients', "tenant:{$tenantId}"])
    ->remember("patient:{$id}", 3600, function() use ($id) {
        return Patient::find($id);
    });

// Invalidate on update
Cache::tags(['patients', "tenant:{$tenantId}"])->flush();
```

### 4. Asset Optimization

**Vite Configuration**:
```javascript
// vite.config.js
export default {
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    'vendor': ['chart.js', 'sweetalert2'],
                    'livewire': ['@livewire/livewire'],
                },
            },
        },
    },
};
```

---

## Scalability

### Horizontal Scaling

```
                    ┌─────────────┐
                    │ Load Balancer│
                    └──────┬───────┘
                           │
        ┌──────────────────┼──────────────────┐
        │                  │                  │
        ▼                  ▼                  ▼
┌───────────────┐  ┌───────────────┐  ┌───────────────┐
│  App Server 1 │  │  App Server 2 │  │  App Server 3 │
│  (Octane)     │  │  (Octane)     │  │  (Octane)     │
└───────┬───────┘  └───────┬───────┘  └───────┬───────┘
        │                  │                  │
        └──────────────────┼──────────────────┘
                           │
        ┌──────────────────┼──────────────────┐
        │                  │                  │
        ▼                  ▼                  ▼
┌───────────────┐  ┌───────────────┐  ┌───────────────┐
│  PostgreSQL   │  │     Redis     │  │     MinIO     │
│  (Primary)    │  │   (Cache)     │  │   (Storage)   │
└───────────────┘  └───────────────┘  └───────────────┘
```

### Queue Workers

```php
// Offload heavy tasks to queues
dispatch(new SendAppointmentReminder($appointment));
dispatch(new GenerateLabReport($labResult));
dispatch(new ProcessInvoice($invoice));

// Supervisor configuration
[program:worker]
command=php artisan queue:work --sleep=3 --tries=3
numprocs=4
autostart=true
autorestart=true
```

### Database Scaling

**Read Replicas**:
```php
// config/database.php
'pgsql' => [
    'read' => [
        'host' => ['replica1.db.com', 'replica2.db.com'],
    ],
    'write' => [
        'host' => ['primary.db.com'],
    ],
],
```

**Connection Pooling** (PgBouncer):
```ini
[databases]
sanago = host=postgres port=5432 dbname=sanago

[pgbouncer]
pool_mode = transaction
max_client_conn = 1000
default_pool_size = 50  # Higher pool for single database
reserve_pool_size = 10
```

---

## Monitoring & Observability

### Metrics Collection

```php
// Custom metrics with Prometheus
use Spatie\Prometheus\Facades\Prometheus;

Prometheus::counter('appointments_created_total')
    ->inc();

Prometheus::histogram('appointment_duration_seconds')
    ->observe($duration);

Prometheus::gauge('active_patients')
    ->set(Patient::where('is_active', true)->count());
```

### Logging Strategy

```php
// Structured logging
Log::info('Appointment created', [
    'appointment_id' => $appointment->id,
    'patient_id' => $appointment->patient_id,
    'doctor_id' => $appointment->doctor_id,
    'tenant_id' => tenant('id'),
]);

// Error tracking
try {
    $this->processPayment($invoice);
} catch (\Exception $e) {
    Log::error('Payment processing failed', [
        'invoice_id' => $invoice->id,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
    ]);
    throw $e;
}
```

---

## Deployment Architecture

### Production Stack

```
┌─────────────────────────────────────────────────────────┐
│                    CDN (CloudFlare)                     │
└────────────────────────┬────────────────────────────────┘
                         │
┌────────────────────────▼────────────────────────────────┐
│              Load Balancer (AWS ALB)                    │
└────────────────────────┬────────────────────────────────┘
                         │
        ┌────────────────┼────────────────┐
        │                │                │
        ▼                ▼                ▼
┌──────────────┐  ┌──────────────┐  ┌──────────────┐
│ ECS Task 1   │  │ ECS Task 2   │  │ ECS Task 3   │
│ (Docker)     │  │ (Docker)     │  │ (Docker)     │
└──────────────┘  └──────────────┘  └──────────────┘
        │                │                │
        └────────────────┼────────────────┘
                         │
        ┌────────────────┼────────────────┐
        │                │                │
        ▼                ▼                ▼
┌──────────────┐  ┌──────────────┐  ┌──────────────┐
│ RDS Postgres │  │ ElastiCache  │  │      S3      │
│  (Multi-AZ)  │  │   (Redis)    │  │  (Storage)   │
└──────────────┘  └──────────────┘  └──────────────┘
```

---

## Conclusion

SanaGo's architecture is designed for:
- **Performance**: Sub-100ms response times with Octane
- **Security**: Multi-layered protection and encryption
- **Scalability**: Horizontal scaling to thousands of tenants
- **Maintainability**: Clean separation of concerns
- **Reliability**: 99.9% uptime with proper deployment

For implementation details, see the codebase and inline documentation.
