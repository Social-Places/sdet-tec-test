# MiniMart - SDET Technical Assessment

Welcome to the MiniMart SDET technical assessment! This is a realistic e-commerce application built with Vue.js 2, Vuetify, Symfony 6.4, and PHP 8.3 that simulates a real-world testing environment.

## 🎯 Assessment Overview

**Objective:** Evaluate your ability to identify bugs, analyze test quality, and improve test coverage in a realistic codebase.

### Your Tasks
1. **Bug Discovery** Find and document bugs in the application
2. **Test Quality Analysis** Identify problematic tests and explain issues
3. **Test Improvement** Fix or write better tests for identified problems
4. **Testing Strategy** Recommend testing improvements
5. **Documentation** Clearly document your findings

## 🚀 Quick Start

### Prerequisites
- Docker & Docker Compose
- Git

### Setup Instructions

1. **Mirror the repository and start the application:**
```bash
git clone <repository-url>
cd minimart-sdet-test
```

2. **Follow the setup guide to get going**

3. **Access the application:**
- Frontend: http://localhost:8082
- Backend API: http://localhost:8000
- Database (phpMyAdmin): http://localhost:8081

4. **Test credentials:**
- Email: `test@example.com`
- Password: `password`

### Running Tests

```bash
# Backend PHPUnit tests
docker-compose exec backend ./vendor/bin/phpunit

# Backend Codeception tests
docker-compose exec backend ./vendor/bin/codecept run

# Frontend Jest tests
docker-compose exec frontend npm run test:unit

# Frontend E2E tests
docker-compose exec frontend npm run test:e2e
```

## 🔍 What You're Looking For

### Application Features
- User registration/login
- Product browsing and search
- Shopping cart functionality
- Order placement
- Order history

## 📝 Assessment Tasks

### Task 1: Bug Discovery
Create a Trello account and a board to match, share the board with the the team when the assessment is complete. The emails are as follow: adele@socialplaces.io, orestes@socialplaces.io, matthew@socialplaces.io, james@socialplaces.io, nik@socialplaces.io
- Board should have the following columns 'Backlog', 'To do', 'Doing', 'Done'. 
- Explore the application and identify bugs, log these bugs on the Trello board.
- It is up to you to determine what information is necessary to add to the tickets. 

### Task 2: Test Quality Analysis
[Mirror](https://docs.github.com/en/repositories/creating-and-managing-repositories/duplicating-a-repository) the repository to your own account. It has to be a private repository and that once you are done add [Orestes Sebele/orestes-za](orestes@socialplaces.io), [James Filmer/socialPJames](james@socialplaces.io), [Nik Seobaran/N1K-5oc1alplac3s](nik@socialplaces.io), [Matthew Henshilwood/matt-socialplaces](matthew@socialplaces.io) and [Adele Truscott/AdeleSocial](adele@socialplaces.io) as collaborators.
- Your assessment should be on a seperate branch to master/main
- Use your preferred client to clone the forked repository
- Follow the instructions to run your project
- Review the existing test files and identify issues and fix them.

**Analyze these test files:**
- `backend/tests/Unit/CartServiceTest.php`
- `backend/tests/Api/ProductCest.php`
- `frontend/tests/unit/ProductCard.spec.js`
- `frontend/tests/e2e/shopping-flow.spec.js`

### Task 3: Test cases
Create a google sheet:
- Complete test cases for one or more sections of the application. Any section(s) of your choice is fine.
- After the test has been completed ensure that the team has access to view it.

## 📁 Project Structure

```
minimart-sdet-test/
├── backend/                 # Symfony PHP API
│   ├── src/                # Source code
│   │   ├── Controller/     # API controllers
│   │   ├── Entity/        # Database entities
│   │   └── Service/       # Business logic
│   └── tests/             # PHP tests
│       ├── Unit/          # PHPUnit unit tests
│       └── Api/           # Codeception API tests
├── frontend/               # Vue.js application
│   ├── src/               # Source code
│   │   ├── components/    # Vue components
│   │   ├── views/         # Page components
│   │   └── store/         # Vuex store
│   └── tests/             # JavaScript tests
│       ├── unit/          # Jest unit tests
│       └── e2e/           # Cypress E2E tests
└── database/              # Database setup
    └── init.sql           # Initial data
```

## 🧪 Testing Framework Guide

### Backend Testing (PHP)
- **PHPUnit** - Unit tests for services and business logic
- **Codeception** - API and integration tests

### Frontend Testing (JavaScript)
- **Jest** - Unit tests for Vue components
- **Cypress** - End-to-end browser tests

## ⚠️ Important Notes

- **This is a codebase with intentional bugs and test issues**
- **Don't fix bugs in the main code, just report them**

## 📋 Deliverables

1. Repo Project with automated tests included
2. Link and subsequent access to a Trello board with discovered bugs
3. Link and access to excel sheet(s) that contains test cases.

---

Good luck with your assessment! We look forward to reviewing your findings and testing expertise.
