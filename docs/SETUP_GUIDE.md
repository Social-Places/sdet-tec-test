# MiniMart SDET Assessment - Setup Guide for Assessors

## 📋 Pre-Assessment Checklist

### Environment Preparation
1. **Docker & Docker Compose** installed on assessment machine
2. **Git** configured and accessible
3. **Stable internet connection** for Docker image downloads
4. **Browser** (Chrome/Firefox recommended for Cypress tests)
5. **Text editor/IDE** for candidate to review code

### Repository Setup
1. Clone this repository to assessment environment
2. Verify all files are present (see structure below)
3. Test Docker setup by running `docker-compose up -d`
4. Confirm all services start successfully
5. Access frontend at http://localhost:8082 to verify setup

## 🚀 Candidate Environment Setup

### Standard Setup Process
```bash
# 1. Start all services
docker-compose up -d
docker-compose exec backend ./vendor/bin/codecept build

# 2. Wait for services to be ready (check logs)
docker-compose logs backend | grep "Application ready"

# 3. Verify services are running
docker-compose ps

# 4. Test database connection
docker-compose exec database mysql -uroot -ppassword -e "USE minimart; SELECT COUNT(*) FROM products;"
```

### Verification Steps
- Frontend accessible at http://localhost:8080
- Backend API responds at http://localhost:8000/api/products
- Database has sample data (8 products, 3 users, 3 categories)
- All tests can be executed (see commands in README.md)

## 🐛 Troubleshooting Common Issues

### Port Conflicts
If ports 8080, 8000, or 3306 are in use:
```bash
# Check port usage
lsof -i :8080
lsof -i :8000
lsof -i :3306

# Stop conflicting services or modify docker-compose.yml ports
```

### Docker Issues
```bash
# Clean Docker environment
docker-compose down -v
docker system prune -f

# Rebuild containers
docker-compose build --no-cache
docker-compose up -d
```

### Database Connection Issues
```bash
# Reset database
docker-compose down -v
docker-compose up -d database
# Wait 30 seconds for MySQL to initialize
docker-compose up -d
```
