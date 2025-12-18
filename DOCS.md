# 📚 SanaGo Documentation Index

This document provides a quick reference to all documentation in the SanaGo project.

## 📖 Main Documentation

### 1. [README.md](./README.md)
**Purpose**: Main project documentation and getting started guide

**Contains**:
- Project overview and features
- Technology stack
- Multi-tenancy architecture (single-database with tenant_id scoping)
- Installation instructions (Docker & local setup)
- Configuration guide
- User roles and permissions
- Core modules overview
- Deployment guide
- Security features
- Testing instructions

**When to read**: Start here for project overview and setup instructions

---

### 2. [ARCHITECTURE.md](./ARCHITECTURE.md)
**Purpose**: In-depth technical architecture documentation

**Contains**:
- System architecture overview
- Single-database multi-tenant implementation details
- Application layers (Livewire, Services, Models)
- Database design and schema
- Security architecture (authentication, authorization, encryption)
- Performance optimization strategies
- Scalability patterns
- Monitoring and observability
- Deployment architecture

**When to read**: For understanding technical implementation details and architecture decisions

---

### 3. [.github/WORKFLOWS.md](./.github/WORKFLOWS.md)
**Purpose**: CI/CD pipeline documentation

**Contains**:
- Workflow overview (CI: lint, test | CD: build, deploy)
- Detailed workflow descriptions
- Execution flow diagrams
- Required GitHub Secrets configuration
- Troubleshooting guide
- Best practices for CI/CD

**When to read**: When setting up or troubleshooting GitHub Actions workflows

---

## 🗂️ Documentation Structure

```
SanaGo-v1/
├── README.md              # 📘 Main documentation (start here)
├── ARCHITECTURE.md        # 🏗️ Technical architecture
├── DOCS.md               # 📚 This file - documentation index
└── .github/
    └── WORKFLOWS.md      # 🔄 CI/CD documentation
```

---

## 🎯 Quick Navigation

### For New Developers
1. Read [README.md](./README.md) - Overview and setup
2. Read [ARCHITECTURE.md](./ARCHITECTURE.md) - Technical details
3. Set up local environment following README instructions

### For DevOps/Deployment
1. Read [README.md](./README.md) - Deployment section
2. Read [.github/WORKFLOWS.md](./.github/WORKFLOWS.md) - CI/CD setup
3. Configure GitHub Secrets as documented

### For Understanding Multi-Tenancy
1. Read [README.md](./README.md) - Multi-tenancy overview
2. Read [ARCHITECTURE.md](./ARCHITECTURE.md) - Implementation details
3. Review database schema and global scopes

---

## 📝 Additional Resources

### In-Code Documentation
- **Models**: `app/Models/` - Eloquent models with relationships
- **Livewire Components**: `app/Livewire/` - Component logic
- **Services**: `app/Services/` - Business logic
- **Migrations**: `database/migrations/` - Database schema

### Configuration Files
- **Environment**: `.env.example` - Environment variables template
- **Docker**: `Dockerfile` - Container build configuration
- **Docker Compose**: `docker-compose.yml` - Local development setup
- **Workflows**: `.github/workflows/` - CI/CD pipeline definitions

---

## 🔍 Finding Information

| Topic | Document | Section |
|-------|----------|---------|
| Installation | README.md | Installation |
| Multi-tenancy | README.md, ARCHITECTURE.md | Multi-Tenancy Architecture |
| Database schema | ARCHITECTURE.md | Database Design |
| User roles | README.md | User Roles |
| Security | README.md, ARCHITECTURE.md | Security |
| Deployment | README.md | Deployment |
| CI/CD | .github/WORKFLOWS.md | All sections |
| Performance | ARCHITECTURE.md | Performance Optimization |
| Testing | README.md | Testing |

---

## 🚀 Getting Started Checklist

- [ ] Read README.md overview
- [ ] Set up local development environment
- [ ] Review ARCHITECTURE.md for technical understanding
- [ ] Configure GitHub Secrets (if deploying)
- [ ] Review .github/WORKFLOWS.md for CI/CD setup
- [ ] Run tests to verify setup
- [ ] Deploy to staging/production

---

## 📞 Support

For questions or issues:
- **GitHub Issues**: [Create an issue](https://github.com/your-username/SanaGo-v1/issues)
- **Team Contact**: Contact project maintainers directly
- **Documentation**: Review this index and linked documents

---

**Last Updated**: 2025-12-04  
**Maintained By**: SanaGo Development Team  
**Status**: Active
