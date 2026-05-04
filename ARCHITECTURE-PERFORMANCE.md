# Performance Optimization & Scalability

This document provides detailed information about performance optimization and scalability strategies for SanaGo.

## Performance Optimization

### 1. Laravel Octane with FrankenPHP

**Traditional PHP-FPM**:
```
Request → Bootstrap Laravel → Process → Shutdown
(~50-100ms per request)
```

**Laravel Octane + FrankenPHP**:
```
Bootstrap Laravel (once)
    ↓
Request → Process → Response
(~10-20ms per request)
```

**Configuration** (`octane-supervisor.conf`):
```ini
[program:octane]
process_name=%(program_name)s_%(process_num)02d
command=php /app/artisan octane:frankenphp --workers=4 --max-requests=500
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/app/storage/logs/octane.log
```

### 2. Database Optimization

**Query Optimization**:
```sql
-- Tenant-scoped composite indexes
CREATE INDEX idx_patients_tenant ON patients(tenant_id, id);
CREATE INDEX idx_appointments_tenant_doctor_date ON appointments(tenant_id, doctor_id, appointment_date);
CREATE INDEX idx_appointments_tenant_patient ON appointments(tenant_id, patient_id);
CREATE INDEX idx_lab_requests_tenant_status ON lab_requests(tenant_id, status);

-- Full-text search (tenant-scoped)
CREATE INDEX idx_patients_search ON patients USING GIN(tenant_id, to_tsvector('english', first_name || ' ' || last_name));

-- Foreign key constraints with cascade
ALTER TABLE patients ADD CONSTRAINT fk_patients_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE;
ALTER TABLE appointments ADD CONSTRAINT fk_appointments_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE;
```

### 3. Caching Strategy

**Redis Caching**:
```php
// config/cache.php
return [
    'default' => env('CACHE_STORE', 'redis'),
    'stores' => [
        'redis' => [
            'driver' => 'redis',
            'connection' => 'cache',
            'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_'),
        ],
    ],
];
```

**Caching in Services**:
```php
class DoctorDashboardService
{
    public function getDashboardData($doctorId)
    {
        return Cache::remember("doctor:{$doctorId}:dashboard", now()->hourly(), function () use ($doctorId) {
            return [
                'appointments' => Appointment::where('doctor_id', $doctorId)
                    ->where('appointment_date', '>=', now()->startOfDay())
                    ->where('appointment_date', '<=', now()->endOfDay())
                    ->with('patient')
                    ->get(),
                'patients' => Patient::where('assigned_doctor_id', $doctorId)
                    ->where('is_active', true)
                    ->count(),
            ];
        });
    }
}
```

### 4. Queue Management

**Supervisor Configuration** (`supervisord.conf`):
```ini
[program:laravel-queue-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /app/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/app/storage/logs/laravel-queue-worker.log
stopwaitsecs=3600
```

---

## Scalability

### Horizontal Scaling

**Docker Swarm/Kubernetes Deployment**:
```yaml
# docker-stack.yml
version: '3.8'
services:
  app:
    image: sanago:latest
    deploy:
      replicas: 3
      update_config:
        parallelism: 1
        delay: 10s
      restart_policy:
        condition: on-failure
    environment:
      - DB_HOST=db
      - REDIS_HOST=redis
    networks:
      - sanago-network

  nginx:
    image: nginx:alpine
    deploy:
      replicas: 2
      placement:
        constraints: [node.role == manager]
    ports:
      - "80:80"
      - "443:443"
    networks:
      - sanago-network

  db:
    image: postgres:15
    deploy:
      placement:
        constraints: [node.role == manager]
    volumes:
      - db_data:/var/lib/postgresql/data
    networks:
      - sanago-network

  redis:
    image: redis:alpine
    deploy:
      placement:
        constraints: [node.role == manager]
    volumes:
      - redis_data:/data
    networks:
      - sanago-network
```

### Load Balancing

**Nginx Configuration**:
```nginx
upstream php-fpm {
    least_conn;
    server app1:8000;
    server app2:8000;
    server app3:8000;
}

server {
    listen 80;
    server_name sanago.com *.sanago.com;
    
    location / {
        proxy_pass http://php-fpm;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
    
    location ~ \.php$ {
        proxy_pass http://php-fpm;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

### Database Sharding Strategy

**Tenant-based Sharding**:
```php
// config/database.php
return [
    'default' => 'tenant',
    'connections' => [
        'central' => [
            'driver' => 'pgsql',
            'host' => 'pgsql-central',
            'database' => 'sanago_central',
            'username' => 'sanago',
            'password' => 'secret',
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],
        'shards' => [
            'shard1' => [
                'driver' => 'pgsql',
                'host' => 'pgsql-shard1',
                'database' => 'sanago_shard1',
                'username' => 'sanago',
                'password' => 'secret',
                'charset' => 'utf8',
                'prefix' => '',
                'schema' => 'public',
                'sslmode' => 'prefer',
            ],
            'shard2' => [
                'driver' => 'pgsql',
                'host' => 'pgsql-shard2',
                'database' => 'sanago_shard2',
                'username' => 'sanago',
                'password' => 'secret',
                'charset' => 'utf8',
                'prefix' => '',
                'schema' => 'public',
                'sslmode' => 'prefer',
            ],
        ],
    ],
];
```

**Shard Routing**:
```php
class ShardManager
{
    public static function getConnectionForTenant($tenantId)
    {
        $shardId = 'shard' . ($tenantId % 2 + 1);
        return config('database.connections.shards.' . $shardId);
    }
}
```
