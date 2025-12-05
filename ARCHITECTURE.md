# 🏗️ SanaGo Architecture Documentation

This document provides an in-depth technical overview of the SanaGo Hospital Management System architecture.

## Table of Contents

- [System Overview](#system-overview)
- [Multi-Tenancy Architecture](#multi-tenancy-architecture)
- [Application Layers](#application-layers)
- [Database Design](#database-design)
- [Security Architecture](#security-architecture)
- [Performance Optimization](#performance-optimization)
- [Scalability](#scalability)

---

## System Overview

SanaGo is built on a modern, layered architecture designed for:
- **Scalability**: Horizontal scaling across multiple servers
- **Security**: Multi-layered security with encryption and isolation
- **Performance**: Sub-100ms response times with Octane
- **Maintainability**: Clean separation of concerns

### Technology Decisions

| Decision | Technology | Rationale |
|----------|-----------|-----------|
| **Framework** | Laravel 12 | Mature ecosystem, excellent ORM, built-in security |
| **Frontend** | Livewire 3 | Full-stack reactivity without JavaScript complexity |
| **Server** | FrankenPHP + Octane | 3-4x faster than traditional PHP-FPM |
| **Database** | PostgreSQL | ACID compliance, JSON support, excellent for multi-tenancy |
| **Cache** | Redis | In-memory performance, pub/sub for real-time features |
| **Storage** | MinIO (S3) | Self-hosted, S3-compatible, cost-effective |
| **Multi-Tenancy** | Stancl Tenancy | Single-database with tenant_id scoping for optimal performance |

---

## Multi-Tenancy Architecture

### Single-Database Multi-Tenant Model

All tenants share a single PostgreSQL database with complete data isolation via tenant_id scoping:

```
Single Database (sanago)
├── Central Tables (no tenant_id)
│   ├── tenants (id, name, created_at, data)
│   ├── domains (id, domain, tenant_id)
│   └── subscriptions (id, tenant_id, plan, status)
│
└── Tenant-Scoped Tables (with tenant_id column)
    ├── users (tenant_id, ...)
    ├── patients (tenant_id, ...)
    ├── appointments (tenant_id, ...)
    ├── medical_records (tenant_id, ...)
    ├── prescriptions (tenant_id, ...)
    ├── lab_requests (tenant_id, ...)
    ├── medications (tenant_id, ...)
    ├── invoices (tenant_id, ...)
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

### 1. Presentation Layer (Livewire Components)

```
resources/views/livewire/
├── tenants/
│   ├── doctor/
│   │   ├── Index.php              # Dashboard
│   │   ├── DoctorAppointment.php  # Appointment management
│   │   ├── MedicalRecord.php      # EMR interface
│   │   └── Patient.php            # Patient list
│   ├── pharmacist/
│   │   ├── Dashboard.php
│   │   ├── DispenseMedication.php
│   │   └── ManageDrugs.php
│   └── ... (other roles)
└── landlord/
    ├── Dashboard.php              # Landlord overview
    ├── ManageTenants.php          # Tenant CRUD
    └── ManageSubscription.php     # Billing
```

**Livewire Component Structure**:
```php
class DoctorAppointment extends Component
{
    // Properties (reactive state)
    public $appointments;
    public $selectedDate;
    
    // Lifecycle hooks
    public function mount() { ... }
    
    // Actions (user interactions)
    public function createAppointment() { ... }
    public function cancelAppointment($id) { ... }
    
    // Computed properties
    public function getTodayAppointmentsProperty() { ... }
    
    // Rendering
    public function render() {
        return view('livewire.tenants.doctor.appointment');
    }
}
```

### 2. Business Logic Layer (Services)

```php
app/Services/
├── AppointmentService.php
├── BillingService.php
├── LabService.php
├── PrescriptionService.php
└── PatientService.php
```

**Example Service**:
```php
class AppointmentService
{
    public function createAppointment(array $data): Appointment
    {
        // Validation
        $this->validateAppointmentData($data);
        
        // Business logic
        $this->checkDoctorAvailability($data['doctor_id'], $data['date']);
        
        // Create appointment
        $appointment = Appointment::create($data);
        
        // Side effects
        $this->sendConfirmationEmail($appointment);
        $this->createInvoice($appointment);
        
        return $appointment;
    }
}
```

### 3. Data Access Layer (Models)

```php
app/Models/
├── Patient.php
├── Appointment.php
├── MedicalRecord.php
├── Prescription.php
├── LabRequest.php
└── ... (30+ models)
```

**Model Relationships**:
```php
class Patient extends Model
{
    use BelongsToTenant;  // Automatically adds global scope for tenant_id
    
    protected $fillable = ['tenant_id', 'first_name', 'last_name', ...];
    
    // Global scope automatically applied
    protected static function booted()
    {
        static::addGlobalScope('tenant', function ($query) {
            if (tenancy()->initialized) {
                $query->where('tenant_id', tenant('id'));
            }
        });
    }
    
    // Relationships (automatically scoped)
    public function appointments() {
        return $this->hasMany(Appointment::class);
    }
    
    public function medicalRecords() {
        return $this->hasMany(MedicalRecord::class);
    }
    
    // Accessors/Mutators
    public function getFullNameAttribute() {
        return "{$this->first_name} {$this->last_name}";
    }
    
    // Scopes
    public function scopeActive($query) {
        return $query->where('is_active', true);
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
