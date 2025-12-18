# 🏥 SanaGo - Multi-Tenant Hospital Management System

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-3.x-4E56A6?style=for-the-badge&logo=livewire&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-15-336791?style=for-the-badge&logo=postgresql&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=for-the-badge&logo=docker&logoColor=white)

**A modern, scalable, multi-tenant hospital management system built with Laravel, Livewire, and FrankenPHP**

[Features](#-features) • [Architecture](#-architecture) • [Installation](#-installation) • [Documentation](#-documentation) • [Contributing](#-contributing)

</div>

---

## 📖 Table of Contents

- [Overview](#-overview)
- [Key Features](#-key-features)
- [Technology Stack](#-technology-stack)
- [Architecture](#-architecture)
- [Project Structure](#-project-structure)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [User Roles](#-user-roles)
- [Core Modules](#-core-modules)
- [API Documentation](#-api-documentation)
- [Deployment](#-deployment)
- [Security](#-security)
- [Contributing](#-contributing)
- [License](#-license)

---

## 🎯 Overview

**SanaGo** is a comprehensive, multi-tenant Hospital Management System (HMS) designed to streamline healthcare operations across multiple hospitals or clinics. Built on modern web technologies, it provides a complete solution for patient management, appointments, medical records, laboratory operations, pharmacy management, and billing.

### What Makes SanaGo Different?

- **🏢 Multi-Tenancy**: Single-database architecture with complete tenant isolation using tenant_id scoping for all data
- **⚡ Real-time Updates**: Built with Livewire for reactive, SPA-like experience without JavaScript complexity
- **🚀 High Performance**: Powered by Laravel Octane + FrankenPHP for blazing-fast response times
- **🌍 Internationalization**: Full multi-language support (English, French, Spanish) with easy extensibility
- **🔒 Enterprise Security**: Role-based access control, data encryption, audit logging, and HIPAA-compliant features
- **📊 Advanced Analytics**: Built-in monitoring with Prometheus, Grafana, and comprehensive reporting
- **☁️ Cloud-Native**: Docker-ready with S3-compatible storage (MinIO) and horizontal scalability

---

## ✨ Key Features

### 🏥 Clinical Management
- **Patient Registration & Records**: Comprehensive patient demographics, medical history, and encrypted sensitive data
- **Appointment Scheduling**: Multi-doctor calendar with conflict detection and automated reminders
- **Electronic Medical Records (EMR)**: Digital charting, vital signs tracking, and attachment support
- **Prescription Management**: E-prescribing with drug interaction warnings and dosage calculations
- **Laboratory Integration**: Test ordering, result management, and automated reporting

### 💊 Pharmacy & Inventory
- **Medication Dispensing**: Track prescriptions, stock levels, and expiry dates
- **Inventory Management**: Real-time stock tracking with low-stock alerts
- **Billing Integration**: Automatic invoice generation for dispensed medications

### 🔬 Laboratory Management
- **Test Catalog**: Customizable test definitions with normal ranges
- **Sample Tracking**: Barcode support and chain-of-custody logging
- **Result Entry**: Multi-format results (numeric, text, images) with quality control
- **Report Generation**: Automated PDF reports with doctor signatures

### 🏨 Inpatient Management
- **Ward & Bed Management**: Real-time bed availability and allocation
- **Admission/Discharge**: Complete admission workflow with discharge summaries
- **Nursing Care**: Vital signs monitoring and care plan tracking

### 💰 Billing & Finance
- **Invoice Generation**: Itemized billing for services, medications, and procedures
- **Payment Processing**: Multiple payment methods with receipt generation
- **Revenue Analytics**: Real-time financial dashboards and reports

### 👥 User Management
- **Role-Based Access Control (RBAC)**: 7 distinct roles with granular permissions
- **Multi-Factor Authentication (MFA)**: TOTP-based 2FA for enhanced security
- **Activity Logging**: Comprehensive audit trail of all user actions
- **Shift Management**: Staff scheduling and attendance tracking

### 📊 Analytics & Reporting
- **Real-time Dashboards**: Role-specific KPIs and metrics
- **Custom Reports**: Flexible report builder with export options
- **Performance Monitoring**: Prometheus metrics and Grafana visualizations
- **Log Aggregation**: Centralized logging with Loki and Promtail

### 🌐 Multi-Tenancy Features
- **Tenant Isolation**: Complete data separation using tenant_id scoping in a single database
- **Subdomain Routing**: Each tenant gets a unique subdomain (e.g., `hospital1.sanago.com`)
- **Custom Branding**: Per-tenant logos, colors, and themes
- **Efficient Scaling**: Single database with optimized indexing and query performance

---

## 🛠️ Technology Stack

### Backend
| Technology | Version | Purpose |
|------------|---------|---------|
| **Laravel** | 12.x | PHP framework providing the foundation |
| **Laravel Octane** | Latest | High-performance application server |
| **FrankenPHP** | 1.0+ | Modern PHP application server with HTTP/2 & HTTP/3 |
| **Livewire** | 3.x | Full-stack framework for dynamic interfaces |
| **Livewire Flux** | 2.x | Component library for beautiful UIs |
| **Stancl Tenancy** | Latest | Multi-tenancy package for Laravel |

### Frontend
| Technology | Purpose |
|------------|---------|
| **Tailwind CSS** | 4.x | Utility-first CSS framework |
| **Alpine.js** | Lightweight JavaScript framework (via Livewire) |
| **Vite** | Modern build tool for assets |
| **Chart.js** | Data visualization and charts |
| **SweetAlert2** | Beautiful alerts and modals |
| **Heroicons** | SVG icon library |

### Database & Storage
| Technology | Purpose |
|------------|---------|
| **PostgreSQL** | 15+ | Primary relational database |
| **Redis** | Caching, sessions, and queues |
| **MinIO** | S3-compatible object storage for files |

### DevOps & Monitoring
| Technology | Purpose |
|------------|---------|
| **Docker** | Containerization |
| **Supervisor** | Process management for Octane & workers |
| **Prometheus** | Metrics collection |
| **Grafana** | Metrics visualization |
| **Loki** | Log aggregation |
| **cAdvisor** | Container metrics |

### Security & Encryption
| Technology | Purpose |
|------------|---------|
| **Spatie CipherSweet** | Field-level encryption for sensitive data |
| **Laravel Sanctum** | API authentication |
| **TOTP** | Two-factor authentication |

---

## 🏗️ Architecture

### Multi-Tenancy Architecture

SanaGo uses a **single-database multi-tenant** approach with tenant_id scoping for optimal performance:

```
┌─────────────────────────────────────────────────────────────┐
│                   Single PostgreSQL Database                │
│                                                              │
│  ┌────────────────────────────────────────────────────┐    │
│  │  Central Tables (No tenant_id)                     │    │
│  │  - tenants                                          │    │
│  │  - domains                                          │    │
│  │  - subscriptions                                    │    │
│  └────────────────────────────────────────────────────┘    │
│                                                              │
│  ┌────────────────────────────────────────────────────┐    │
│  │  Tenant-Scoped Tables (with tenant_id)             │    │
│  │                                                      │    │
│  │  users (tenant_id, name, email, ...)                │    │
│  │  ├─ Tenant 1: hospital-a                            │    │
│  │  ├─ Tenant 2: clinic-b                              │    │
│  │  └─ Tenant 3: medical-c                             │    │
│  │                                                      │    │
│  │  patients (tenant_id, name, dob, ...)               │    │
│  │  ├─ Tenant 1: hospital-a patients                   │    │
│  │  ├─ Tenant 2: clinic-b patients                     │    │
│  │  └─ Tenant 3: medical-c patients                    │    │
│  │                                                      │    │
│  │  appointments (tenant_id, patient_id, ...)          │    │
│  │  prescriptions (tenant_id, patient_id, ...)         │    │
│  │  lab_results (tenant_id, patient_id, ...)           │    │
│  │  ... (all tenant data with automatic scoping)       │    │
│  └────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────┘

🔒 Automatic Tenant Scoping:
- Global scopes ensure queries are automatically filtered by tenant_id
- Middleware identifies tenant from subdomain and sets context
- All Eloquent queries automatically include WHERE tenant_id = ?
- Complete data isolation without database overhead
```

### Request Flow

```
User Request (hospital-a.sanago.com)
         │
         ▼
┌─────────────────────┐
│  Caddy/FrankenPHP   │ ◄── Handles SSL, HTTP/2, routing
└─────────────────────┘
         │
         ▼
┌─────────────────────┐
│  Laravel Octane     │ ◄── High-performance PHP server
└─────────────────────┘
         │
         ▼
┌─────────────────────┐
│  Tenancy Middleware │ ◄── Identifies tenant from subdomain
└─────────────────────┘
         │
         ▼
┌─────────────────────┐
│  Set Tenant Context │ ◄── Sets tenant_id for query scoping
└─────────────────────┘
         │
         ▼
┌─────────────────────┐
│  Livewire Component │ ◄── Renders UI and handles interactions
└─────────────────────┘
         │
         ▼
┌─────────────────────┐
│  Response (HTML)    │ ◄── Sent back to user
└─────────────────────┘
```

### Data Flow

```
┌──────────────┐
│   Browser    │
└──────┬───────┘
       │
       │ HTTP Request
       ▼
┌──────────────┐
│  Livewire    │ ◄── Reactive components
└──────┬───────┘
       │
       │ Eloquent ORM
       ▼
┌──────────────┐
│  PostgreSQL  │ ◄── Tenant-specific database
└──────┬───────┘
       │
       │ File Storage
       ▼
┌──────────────┐
│    MinIO     │ ◄── S3-compatible object storage
└──────────────┘
```

---

## 📁 Project Structure

```
SanaGo-v1/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # Traditional controllers (minimal use)
│   │   └── Middleware/        # Custom middleware
│   ├── Livewire/              # 🎯 Core application logic
│   │   ├── Auth/              # Authentication components
│   │   ├── LandLord/          # Central tenant management
│   │   ├── Tenants/           # Tenant-specific components
│   │   │   ├── Admin/         # Hospital administrator features
│   │   │   ├── Doctor/        # Doctor-specific features
│   │   │   ├── Nurse/         # Nursing features
│   │   │   ├── Pharmacist/    # Pharmacy management
│   │   │   ├── LabTechnician/ # Laboratory features
│   │   │   └── Receptionist/  # Front desk operations
│   │   ├── Component/         # Shared UI components
│   │   └── [Public Pages]/    # Marketing pages (Home, Pricing, etc.)
│   ├── Models/                # 🗄️ Eloquent models (30+ models)
│   │   ├── Patient.php
│   │   ├── Appointment.php
│   │   ├── MedicalRecord.php
│   │   ├── Prescription.php
│   │   ├── LabRequest.php
│   │   ├── Medication.php
│   │   ├── Tenant.php
│   │   └── ...
│   ├── Services/              # Business logic services
│   ├── Traits/                # Reusable traits
│   └── Providers/             # Service providers
├── database/
│   ├── migrations/            # Database schema
│   │   ├── [central]/         # Central app migrations
│   │   └── tenant/            # Tenant-specific migrations
│   ├── seeders/               # Database seeders
│   └── factories/             # Model factories for testing
├── resources/
│   ├── views/
│   │   ├── livewire/          # Livewire component views
│   │   ├── components/        # Blade components
│   │   └── layouts/           # Layout templates
│   ├── lang/                  # 🌍 Translations
│   │   ├── en/                # English
│   │   ├── fr/                # French
│   │   └── es/                # Spanish
│   └── css/                   # Stylesheets
├── routes/
│   ├── web.php                # Central app routes
│   ├── tenant.php             # Tenant-specific routes
│   └── auth.php               # Authentication routes
├── config/
│   ├── tenancy.php            # Multi-tenancy configuration
│   ├── database.php           # Database connections
│   └── ...
├── storage/
│   ├── app/                   # Application files
│   ├── logs/                  # Application logs
│   └── framework/             # Framework cache
├── public/
│   ├── build/                 # Compiled assets (Vite)
│   └── storage/               # Public file storage
├── tests/                     # PHPUnit tests
├── monitoring/                # 📊 Monitoring configs
│   ├── prometheus.yml
│   ├── grafana/
│   └── loki-config.yml
├── .github/
│   └── workflows/             # CI/CD pipelines
│       └── docker-publish.yml
├── docker-compose.yml         # Local development setup
├── Dockerfile                 # Production Docker image
├── .dockerignore              # Docker build exclusions
├── octane-supervisor.conf     # Supervisor configuration
└── README.md                  # This file
```

---

## 🚀 Installation

### Prerequisites

- **PHP** 8.2 or higher
- **Composer** 2.x
- **Node.js** 18+ and NPM
- **PostgreSQL** 15+
- **Redis** 7+
- **Docker** & Docker Compose (for containerized setup)

### Option 1: Docker Setup (Recommended)

```bash
# 1. Clone the private repository (requires authentication)
git clone https://github.com/your-username/SanaGo-v1.git
cd SanaGo-v1

# Note: You'll need access to this private repository
# Contact the repository owner for access

# 2. Copy environment file
cp .env.example .env

# 3. Generate application key
php artisan key:generate

# 4. Start all services
docker-compose up -d

# 5. Run central migrations
docker exec sanago-octane php artisan migrate

# 6. Create your first tenant
docker exec sanago-octane php artisan tenants:create hospital-a

# 7. Run tenant migrations
docker exec sanago-octane php artisan tenants:migrate

# 8. Access the application
open http://localhost:8000
```

### Option 2: Local Setup

```bash
# 1. Clone the private repository and install dependencies
git clone https://github.com/your-username/SanaGo-v1.git
cd SanaGo-v1
composer install
npm install

# 2. Configure environment
cp .env.example .env
php artisan key:generate

# 3. Configure database in .env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=sanago_central
DB_USERNAME=your_user
DB_PASSWORD=your_password

# 4. Run migrations
php artisan migrate
php artisan tenants:migrate

# 5. Build assets
npm run build

# 6. Start development servers
php artisan octane:start --host=0.0.0.0 --port=8000
# In another terminal:
npm run dev
```

### Creating Tenants

```bash
# Create a new tenant (hospital/clinic)
php artisan tenants:create hospital-name

# This creates:
# - A new tenant record in the tenants table
# - A unique tenant_id for data scoping
# - A domain: hospital-name.localhost
# - Seeds initial tenant data

# Seed tenant with sample data (optional)
php artisan tenants:seed --tenants=hospital-name
```

---

## ⚙️ Configuration

### Environment Variables

Key configuration in `.env`:

```bash
# Application
APP_NAME=SanaGo
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://sanago.com

# Database (Central)
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=sanago_central
DB_USERNAME=sanago
DB_PASSWORD=secret

# Redis
REDIS_HOST=redis
REDIS_PORT=6379

# S3/MinIO (File Storage)
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=sanago-files
AWS_ENDPOINT=http://minio:9000
AWS_USE_PATH_STYLE_ENDPOINT=true

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525

# Tenancy (Single Database Multi-Tenant)
TENANCY_CENTRAL_DOMAINS=localhost,127.0.0.1,sanago.com

# Octane
OCTANE_SERVER=frankenphp
```

### Multi-Tenancy Configuration

Edit `config/tenancy.php` to customize:

- **Central domains**: Domains that host the landlord app
- **Tenant identification**: Subdomain-based tenant resolution
- **Global scopes**: Automatic tenant_id filtering for all queries
- **Storage**: S3 disk configuration for tenant file separation

---

## 👥 User Roles

SanaGo implements a comprehensive role-based access control system:

| Role | Description | Key Permissions |
|------|-------------|-----------------|
| **🏢 Landlord** | System administrator | Manage tenants, subscriptions, global settings |
| **👨‍💼 Admin** | Hospital administrator | Manage staff, departments, system configuration |
| **👨‍⚕️ Doctor** | Medical practitioner | Patient consultations, prescriptions, medical records |
| **👩‍⚕️ Nurse** | Nursing staff | Vital signs, patient care, medication administration |
| **💊 Pharmacist** | Pharmacy staff | Dispense medications, inventory management |
| **🔬 Lab Technician** | Laboratory staff | Process tests, enter results, generate reports |
| **📋 Receptionist** | Front desk staff | Patient registration, appointments, billing |

### Permission Matrix

| Feature | Landlord | Admin | Doctor | Nurse | Pharmacist | Lab Tech | Receptionist |
|---------|----------|-------|--------|-------|------------|----------|--------------|
| Manage Tenants | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Manage Staff | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Patient Records | ❌ | ✅ | ✅ | ✅ | ❌ | ❌ | ✅ |
| Prescriptions | ❌ | ❌ | ✅ | ❌ | ✅ | ❌ | ❌ |
| Lab Tests | ❌ | ✅ | ✅ | ❌ | ❌ | ✅ | ❌ |
| Billing | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ | ✅ |

---

## 📦 Core Modules

### 1. Patient Management
**Models**: `Patient`, `MedicalRecord`, `Vital`, `Admission`

**Features**:
- Patient registration with demographics
- Medical history tracking
- Allergy and medication tracking
- Family history
- Insurance information
- Encrypted sensitive data (SSN, etc.)

### 2. Appointment System
**Models**: `Appointment`

**Features**:
- Multi-doctor scheduling
- Conflict detection
- Recurring appointments
- SMS/Email reminders
- Walk-in support
- Cancellation management

### 3. Electronic Medical Records (EMR)
**Models**: `MedicalRecord`, `MedicalRecordAttachment`

**Features**:
- SOAP note format
- Vital signs integration
- Diagnosis coding (ICD-10)
- Procedure coding (CPT)
- Document attachments
- Version history

### 4. Laboratory Management
**Models**: `LabTestDefinition`, `LabRequest`, `LabResult`, `LabResultAttachment`

**Features**:
- Test catalog management
- Sample tracking
- Result entry (numeric, text, images)
- Normal range validation
- Critical value alerts
- PDF report generation

### 5. Pharmacy & Medication
**Models**: `Medication`, `Prescription`, `PrescriptionItem`, `Dispensation`

**Features**:
- Drug database
- E-prescribing
- Drug interaction checking
- Inventory management
- Expiry tracking
- Dispensing workflow

### 6. Billing & Invoicing
**Models**: `Invoice`, `RevenueSummary`

**Features**:
- Service-based billing
- Insurance claims
- Payment tracking
- Receipt generation
- Revenue analytics

### 7. Inpatient Management
**Models**: `Ward`, `Bed`, `BedType`, `Admission`

**Features**:
- Ward management
- Bed allocation
- Admission workflow
- Discharge summaries
- Transfer management

### 8. Staff Management
**Models**: `User`, `Department`, `UserShift`, `UserActivity`

**Features**:
- Staff profiles
- Department assignment
- Shift scheduling
- Activity logging
- Performance tracking

---

## 🔌 API Documentation

SanaGo provides a RESTful API for integration with external systems.

### Authentication

```bash
# Login to get API token
POST /api/login
{
  "email": "doctor@hospital.com",
  "password": "password"
}

# Response
{
  "token": "1|abc123...",
  "user": {...}
}

# Use token in subsequent requests
Authorization: Bearer 1|abc123...
```

### Example Endpoints

```bash
# Get patient list
GET /api/patients

# Get patient details
GET /api/patients/{id}

# Create appointment
POST /api/appointments
{
  "patient_id": 1,
  "doctor_id": 2,
  "appointment_date": "2024-12-10 10:00:00",
  "reason": "Follow-up consultation"
}

# Get lab results
GET /api/lab-results?patient_id=1

# Health check
GET /api/health
```

Full API documentation available at `/api/documentation` (when enabled).

---

## 🚢 Deployment

### Production Deployment with Docker

See [DOCKER_DEPLOYMENT.md](DOCKER_DEPLOYMENT.md) for comprehensive deployment guide.

**Quick Deploy**:

```bash
# Pull the latest image from GitHub Container Registry (requires authentication)
docker login ghcr.io -u your-username
docker pull ghcr.io/your-username/sanago-v1:latest

# Run with environment variables
docker run -d \
  -p 8000:8000 \
  -e APP_KEY=your-key \
  -e DB_HOST=your-db-host \
  -e DB_DATABASE=sanago \
  -e DB_USERNAME=user \
  -e DB_PASSWORD=pass \
  ghcr.io/your-username/sanago-v1:latest
```

### Cloud Platforms

#### Render.com
1. Create new Web Service
2. Use Docker image: `ghcr.io/your-username/sanago-v1:latest`
3. Add environment variables
4. Deploy

#### Fly.io
```bash
fly launch --image ghcr.io/your-username/sanago-v1:latest
fly secrets set APP_KEY=your-key DB_HOST=your-db
fly deploy
```

#### AWS ECS / Kubernetes
See deployment examples in `/deployment` directory.

---

## 🔒 Security

### Security Features

- ✅ **Field-Level Encryption**: Sensitive data encrypted with CipherSweet
- ✅ **Two-Factor Authentication**: TOTP-based 2FA for all users
- ✅ **Role-Based Access Control**: Granular permissions per role
- ✅ **Audit Logging**: Complete trail of all user actions
- ✅ **SQL Injection Protection**: Eloquent ORM with prepared statements
- ✅ **XSS Protection**: Blade templating with automatic escaping
- ✅ **CSRF Protection**: Token-based CSRF prevention
- ✅ **Rate Limiting**: API and login attempt throttling
- ✅ **Secure Sessions**: HTTP-only, secure cookies
- ✅ **Data Isolation**: Complete tenant separation

### HIPAA Compliance Features

- Encrypted data at rest and in transit
- Audit logs for all PHI access
- User authentication and authorization
- Automatic session timeouts
- Data backup and recovery
- Breach notification system

### Security Best Practices

```bash
# 1. Always use HTTPS in production
APP_URL=https://sanago.com

# 2. Set secure session settings
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true

# 3. Enable 2FA for all users
# (Available in user settings)

# 4. Regular security updates
composer update
npm update

# 5. Monitor logs
tail -f storage/logs/laravel.log
```

---

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage

# Run tests in parallel
php artisan test --parallel
```

---

## 📊 Monitoring

### Accessing Monitoring Tools

- **Grafana**: http://localhost:3000 (admin/secret)
- **Prometheus**: http://localhost:9090
- **Loki**: http://localhost:3100

### Key Metrics

- Request latency
- Database query performance
- Queue job processing
- Memory usage
- CPU utilization
- Error rates

---

## 🤝 Contributing

We welcome contributions! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

### Development Workflow

```bash
# 1. Fork the repository
# 2. Create a feature branch
git checkout -b feature/amazing-feature

# 3. Make your changes
# 4. Run tests
php artisan test

# 5. Commit with conventional commits
git commit -m "feat: add amazing feature"

# 6. Push and create PR
git push origin feature/amazing-feature
```

### Code Style

```bash
# Format code with Laravel Pint
./vendor/bin/pint

# Check for issues
./vendor/bin/pint --test
```

---

## 📝 License

This is a **private, proprietary project**. All rights reserved.

Unauthorized copying, distribution, or use of this software is strictly prohibited.

---

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) - The PHP framework
- [Livewire](https://livewire.laravel.com) - Full-stack framework
- [Stancl Tenancy](https://tenancyforlaravel.com) - Multi-tenancy package
- [FrankenPHP](https://frankenphp.dev) - Modern PHP server
- [Tailwind CSS](https://tailwindcss.com) - CSS framework

---

## 📞 Support

For internal team support:

- **Issues**: [GitHub Issues](https://github.com/your-username/SanaGo-v1/issues) (Private repository)
- **Team Discussions**: Contact project maintainers directly
- **Documentation**: See `/docs` directory in this repository

---

## 🗺️ Roadmap

- [ ] Mobile app (React Native)
- [ ] Telemedicine integration
- [ ] AI-powered diagnosis assistance
- [ ] HL7/FHIR integration
- [ ] Insurance claim automation
- [ ] Multi-language support expansion
- [ ] Advanced analytics and ML insights

---

<div align="center">

**Built with ❤️ by the SanaGo Team**

🔒 Private Repository - Internal Use Only

</div>
