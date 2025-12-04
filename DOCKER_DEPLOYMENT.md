# 🐳 Docker Deployment Guide for SanaGo

This guide explains how to deploy SanaGo using Docker and GitHub Container Registry (GHCR).

## 📋 Table of Contents

- [Quick Start](#quick-start)
- [GitHub Container Registry Setup](#github-container-registry-setup)
- [Local Development](#local-development)
- [Production Deployment](#production-deployment)
- [Environment Variables](#environment-variables)
- [Troubleshooting](#troubleshooting)

## 🚀 Quick Start

### Prerequisites

- Docker 20.10+
- Docker Compose 2.0+
- GitHub account (for GHCR)

### Local Development

```bash
# 1. Clone the repository
git clone https://github.com/your-username/SanaGo-v1.git
cd SanaGo-v1

# 2. Copy environment file
cp .env.example .env

# 3. Generate application key
php artisan key:generate

# 4. Start services
docker-compose up -d

# 5. Run migrations
docker exec sanago-octane php artisan migrate

# 6. Access the application
open http://localhost:8000
```

## 🔐 GitHub Container Registry Setup

### 1. Enable GitHub Packages

1. Go to your repository settings
2. Navigate to **Actions** → **General**
3. Under **Workflow permissions**, select:
   - ✅ Read and write permissions
   - ✅ Allow GitHub Actions to create and approve pull requests

### 2. Automatic Image Building

The GitHub Action (`.github/workflows/docker-publish.yml`) automatically builds and pushes images when you:

- Push to `main` or `develop` branches
- Create a new tag (e.g., `v1.0.0`)
- Open a pull request

### 3. Image Tags

Images are automatically tagged as:

- `latest` - Latest commit on main branch
- `main-<sha>` - Specific commit on main
- `develop-<sha>` - Specific commit on develop
- `v1.0.0` - Semantic version tags
- `1.0` - Major.minor version
- `1` - Major version only

### 4. Pull the Image

```bash
# Login to GHCR
echo $GITHUB_TOKEN | docker login ghcr.io -u USERNAME --password-stdin

# Pull the latest image
docker pull ghcr.io/your-username/sanago-v1:latest

# Pull a specific version
docker pull ghcr.io/your-username/sanago-v1:v1.0.0
```

## 🏗️ Build Process Explained

### Multi-Stage Build

The Dockerfile uses a **multi-stage build** to create a lean production image:

#### Stage 1: Builder
```dockerfile
FROM dunglas/frankenphp:1.0-php8.3 AS builder
```

**What happens:**
1. ✅ Installs Composer dependencies (`composer install --no-dev`)
2. ✅ Installs NPM packages (`npm ci`)
3. ✅ Builds Vite assets (`npm run build`)
4. ✅ Optimizes autoloader (`composer dump-autoload --optimize`)

**You DO NOT need to:**
- ❌ Run `composer install` in GitHub Actions
- ❌ Run `npm install` in GitHub Actions
- ❌ Run `npm run build` in GitHub Actions

**Everything is installed inside the Docker image during the build process.**

#### Stage 2: Runtime
```dockerfile
FROM dunglas/frankenphp:1.0-php8.3
```

**What happens:**
1. Copies only the compiled application from builder
2. Installs only runtime dependencies
3. Configures PHP for production (OPcache enabled)
4. Sets up Supervisor for Octane + Queue workers
5. Runs as `www-data` user (non-root)

### Image Size Comparison

| Approach | Image Size | Build Time |
|----------|-----------|------------|
| Without multi-stage | ~2.5 GB | 8-10 min |
| **With multi-stage** | **~800 MB** | **6-8 min** |

## 🌍 Production Deployment

### Option 1: Docker Compose (Simple)

```yaml
# docker-compose.prod.yml
services:
  app:
    image: ghcr.io/your-username/sanago-v1:latest
    ports:
      - "8000:8000"
    environment:
      - APP_ENV=production
      - APP_KEY=${APP_KEY}
      - DB_HOST=your-db-host
      - DB_DATABASE=sanago
      - DB_USERNAME=${DB_USER}
      - DB_PASSWORD=${DB_PASS}
      - REDIS_HOST=redis
    depends_on:
      - redis
      - postgres

  redis:
    image: redis:alpine
    volumes:
      - redis_data:/data

  postgres:
    image: postgres:15
    environment:
      - POSTGRES_DB=sanago
      - POSTGRES_USER=${DB_USER}
      - POSTGRES_PASSWORD=${DB_PASS}
    volumes:
      - postgres_data:/var/lib/postgresql/data

volumes:
  redis_data:
  postgres_data:
```

Deploy:
```bash
docker-compose -f docker-compose.prod.yml up -d
```

### Option 2: Kubernetes

```yaml
# deployment.yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: sanago
spec:
  replicas: 3
  selector:
    matchLabels:
      app: sanago
  template:
    metadata:
      labels:
        app: sanago
    spec:
      containers:
      - name: sanago
        image: ghcr.io/your-username/sanago-v1:latest
        ports:
        - containerPort: 8000
        env:
        - name: APP_KEY
          valueFrom:
            secretKeyRef:
              name: sanago-secrets
              key: app-key
        livenessProbe:
          httpGet:
            path: /api/health
            port: 8000
          initialDelaySeconds: 30
          periodSeconds: 10
        readinessProbe:
          httpGet:
            path: /api/health
            port: 8000
          initialDelaySeconds: 10
          periodSeconds: 5
```

### Option 3: Cloud Platforms

#### Render.com
```bash
# Use the GHCR image directly in Render dashboard
ghcr.io/your-username/sanago-v1:latest
```

#### Fly.io
```bash
fly launch --image ghcr.io/your-username/sanago-v1:latest
```

#### Railway
```bash
# Add the GHCR image in Railway dashboard
ghcr.io/your-username/sanago-v1:latest
```

## 🔧 Environment Variables

### Required Variables

```bash
# Application
APP_NAME=SanaGo
APP_ENV=production
APP_KEY=base64:your-key-here
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=pgsql
DB_HOST=your-db-host
DB_PORT=5432
DB_DATABASE=sanago
DB_USERNAME=your-user
DB_PASSWORD=your-password

# Redis
REDIS_HOST=redis
REDIS_PORT=6379

# S3/MinIO (File Storage)
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=sanago-files
AWS_ENDPOINT=https://s3.amazonaws.com  # or MinIO endpoint
```

### Optional Variables

```bash
# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null

# Queue
QUEUE_CONNECTION=redis

# Session
SESSION_DRIVER=redis
SESSION_LIFETIME=120
```

## 🐛 Troubleshooting

### Issue: Container exits immediately

**Solution:**
```bash
# Check logs
docker logs sanago-octane

# Common causes:
# 1. Missing APP_KEY
docker exec sanago-octane php artisan key:generate

# 2. Database connection failed
# Check DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 3. Permission issues
docker exec sanago-octane chown -R www-data:www-data /app/storage /app/bootstrap/cache
```

### Issue: Assets not loading (404 on CSS/JS)

**Solution:**
```bash
# Rebuild the image to compile assets
docker-compose build --no-cache

# Or manually build assets in container
docker exec sanago-octane npm run build
```

### Issue: Queue workers not processing jobs

**Solution:**
```bash
# Check supervisor status
docker exec sanago-octane supervisorctl status

# Restart workers
docker exec sanago-octane supervisorctl restart worker:*

# Check worker logs
docker exec sanago-octane tail -f /app/storage/logs/worker.log
```

### Issue: GitHub Action fails to push image

**Solution:**
1. Check repository settings → Actions → Workflow permissions
2. Ensure "Read and write permissions" is enabled
3. Verify `GITHUB_TOKEN` has `packages: write` permission

### Issue: Image pull authentication failed

**Solution:**
```bash
# Create a Personal Access Token (PAT) with read:packages scope
# https://github.com/settings/tokens

# Login with PAT
echo YOUR_PAT | docker login ghcr.io -u YOUR_USERNAME --password-stdin

# Or use GitHub CLI
gh auth login
```

## 📊 Monitoring

### Health Check

```bash
# Check if application is healthy
curl http://localhost:8000/api/health

# Expected response:
# {"status":"healthy","timestamp":"2024-12-04T22:00:00+00:00"}
```

### Logs

```bash
# Application logs
docker logs -f sanago-octane

# Octane logs
docker exec sanago-octane tail -f /app/storage/logs/octane.log

# Worker logs
docker exec sanago-octane tail -f /app/storage/logs/worker.log

# Laravel logs
docker exec sanago-octane tail -f /app/storage/logs/laravel.log
```

### Metrics (with Prometheus)

The docker-compose includes Prometheus, Grafana, and other monitoring tools:

- **Grafana**: http://localhost:3000 (admin/secret)
- **Prometheus**: http://localhost:9090
- **cAdvisor**: http://localhost:8080

## 🔒 Security Best Practices

1. ✅ **Never commit `.env` files** - Use `.env.example` as template
2. ✅ **Use secrets management** - Store sensitive data in GitHub Secrets, Vault, or cloud provider secrets
3. ✅ **Run as non-root** - Container runs as `www-data` user
4. ✅ **Pin image versions** - Use specific tags in production (not `latest`)
5. ✅ **Enable HTTPS** - Use reverse proxy (Caddy, Nginx) with SSL certificates
6. ✅ **Regular updates** - Keep base images and dependencies updated
7. ✅ **Scan images** - Use `docker scan` or Trivy for vulnerability scanning

## 📚 Additional Resources

- [Laravel Octane Documentation](https://laravel.com/docs/octane)
- [FrankenPHP Documentation](https://frankenphp.dev)
- [Docker Best Practices](https://docs.docker.com/develop/dev-best-practices/)
- [GitHub Packages Documentation](https://docs.github.com/en/packages)

## 🤝 Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for development guidelines.

## 📄 License

See [LICENSE](LICENSE) file for details.
