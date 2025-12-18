# GitHub Actions Workflows - SanaGo

This document describes the CI/CD pipeline for the SanaGo project.

## 📋 Workflow Overview

The project uses **4 GitHub Actions workflows** organized into **CI** (Continuous Integration) and **CD** (Continuous Deployment) categories:

### 🔍 CI Workflows (Continuous Integration)

#### 1. `ci-lint.yml` - Code Linting
**Triggers:** Push/PR to `main` or `develop` branches

**Purpose:** Ensures code quality and consistent style using Laravel Pint

**Steps:**
- Checkout code
- Setup PHP 8.4
- Install Laravel Pint
- Run Pint in test mode (check only)
- Auto-fix issues if found
- Commit fixes automatically (with `[skip ci]` tag)

---

#### 2. `ci-test.yml` - Run Tests
**Triggers:** Push/PR to `main` or `develop` branches

**Purpose:** Runs PHPUnit tests with coverage reporting

**Steps:**
- Checkout code
- Setup PHP 8.4 with extensions (including Xdebug for coverage)
- Setup Node.js 22
- Install Composer dependencies
- Install NPM dependencies and build assets
- Prepare test environment (copy `.env.example`, generate key)
- Run PHPUnit tests with coverage
- Upload test results as artifacts

**Services:**
- Redis (for cache/queue testing)

---

### 🚀 CD Workflows (Continuous Deployment)

#### 3. `cd-build.yml` - Build & Push Docker Image
**Triggers:** 
- Push to `main` branch
- Version tags (`v*.*.*`)
- Manual dispatch

**Purpose:** Builds Docker image and pushes to GitHub Container Registry (GHCR)

**Steps:**
- Checkout repository
- Setup Docker Buildx (for caching and multi-platform builds)
- Login to GHCR
- Extract metadata (tags, labels)
- Build and push Docker image with caching
- Output image digest

**Image Tags:**
- `latest` (for main branch)
- `main-<sha>` (commit SHA)
- `v1.0.0`, `v1.0`, `v1` (for version tags)

---

#### 4. `cd-deploy.yml` - Deploy to Production
**Triggers:**
- After successful completion of `cd-build.yml`
- Manual dispatch

**Purpose:** Deploys the latest Docker image to production server

**Steps:**
- SSH into production server
- Navigate to project directory (`/var/www/my-app`)
- Pull latest Docker image from GHCR
- Stop old containers
- Start new containers with `docker compose`
- Wait for containers to be healthy
- Run database migrations
- Clear and optimize Laravel caches
- Verify deployment status

**Required Secrets:**
- `SSH_HOST` - Production server hostname/IP
- `SSH_USER` - SSH username
- `SSH_PRIVATE_KEY` - SSH private key for authentication
- `SSH_PORT` (optional, defaults to 22)

---

## 🔄 Workflow Execution Flow

```
┌─────────────────────────────────────────────────────────────┐
│                     Developer Push/PR                        │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
              ┌───────────────────────────────┐
              │   CI: Lint & Test (Parallel)  │
              ├───────────────┬───────────────┤
              │  ci-lint.yml  │  ci-test.yml  │
              └───────────────┴───────────────┘
                              │
                    (If push to main)
                              ▼
              ┌───────────────────────────────┐
              │   CD: Build Docker Image      │
              │      cd-build.yml             │
              └───────────────────────────────┘
                              │
                    (On success)
                              ▼
              ┌───────────────────────────────┐
              │   CD: Deploy to Production    │
              │      cd-deploy.yml            │
              └───────────────────────────────┘
```

---

## 🔐 Required GitHub Secrets

Configure these secrets in your GitHub repository settings:

| Secret Name | Description | Example |
|-------------|-------------|---------|
| `SSH_HOST` | Production server IP/hostname | `192.168.1.100` or `app.example.com` |
| `SSH_USER` | SSH username | `deploy` or `ubuntu` |
| `SSH_PRIVATE_KEY` | SSH private key (full content) | `-----BEGIN OPENSSH PRIVATE KEY-----...` |
| `SSH_PORT` | SSH port (optional) | `22` (default) |

---

## 📦 Docker Image Registry

Images are stored in **GitHub Container Registry (GHCR)**:

```
ghcr.io/your-username/sanago:latest
ghcr.io/your-username/sanago:main-abc1234
ghcr.io/your-username/sanago:v1.0.0
```

To pull the image locally:
```bash
docker pull ghcr.io/your-username/sanago:latest
```

---

## 🛠️ Manual Deployment

You can manually trigger deployments using GitHub Actions UI:

1. Go to **Actions** tab in GitHub
2. Select **CD - Build & Push Docker Image** or **CD - Deploy to Production**
3. Click **Run workflow**
4. Select branch and click **Run workflow**

---

## ✅ Best Practices

1. **Always create PRs** - Let CI run linting and tests before merging
2. **Use semantic versioning** - Tag releases as `v1.0.0`, `v1.1.0`, etc.
3. **Monitor deployments** - Check GitHub Actions logs for deployment status
4. **Test locally first** - Use `docker compose up` to test before pushing
5. **Keep secrets secure** - Never commit secrets to the repository

---

## 🐛 Troubleshooting

### Lint failures
```bash
# Run Pint locally to fix issues
composer global require laravel/pint
pint
```

### Test failures
```bash
# Run tests locally
cp .env.example .env
php artisan key:generate
vendor/bin/phpunit
```

### Build failures
```bash
# Test Docker build locally
docker build -t sanago:test .
```

### Deployment failures
- Check SSH credentials in GitHub Secrets
- Verify production server is accessible
- Check Docker Compose configuration on server
- Review deployment logs in GitHub Actions

---

## 📚 Additional Resources

- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [Docker Documentation](https://docs.docker.com/)
- [Laravel Pint](https://laravel.com/docs/pint)
- [PHPUnit Documentation](https://phpunit.de/)
