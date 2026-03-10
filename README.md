# Surf Trip

Surf Trip is a web application designed to connect people who want to **organize** or **join** surf trips — from short getaways to multi-week surf journeys.

The project is built as an evolving MVP with a clear architectural trajectory:
**Symfony core → API Platform ✓ → React SPA** (in progress).

## Product Vision

Surf Trip addresses a simple but fragmented problem:

Surfers often coordinate trips through messaging apps, spreadsheets, or informal groups. There is no lightweight platform focused on:

* trip discovery by level and dates
* structured organization
* transparent participation
* scalable collaboration

The goal is to build a focused, extensible platform with a clean domain model and a modern API-first architecture.

## Core Features (Current MVP)

* User authentication (JWT-based API, session-based web UI)
* Trip REST API with search and filtering
* Trip creation (location, dates, level requirements, description)
* Trip discovery and detail views
* Participation management
* Admin tools
* Fixtures for realistic development data

## Domain Model

The application is structured around a clear domain foundation with value objects ensuring type safety and business logic encapsulation:

* **User**
* **Trip**
* **TripParticipant**
* (Planned) Message / Discussion
* (Planned) Notification

The objective is to keep business logic encapsulated and progressively expose it through the API Platform layer.

## Architecture Strategy

### Current Architecture (Post-MVP Evolution)

* **Symfony 8** (monolithic with API Platform)
* **API Platform 4.2** (fully integrated)
  - REST resources for User & Trip
  - OpenAPI / Swagger documentation
  - Search, filtering & pagination
  - Granular serialization groups
  - Security-aware operations
* JWT-based authentication
* Twig rendering
* Modular UX components (Stimulus, Turbo, Twig Components)
* Doctrine ORM
* PostgreSQL
* Redis
* FrankenPHP
* CORS-enabled

### Target Architecture (React SPA)

* Symfony API Platform as backend
* React SPA consuming the API
* Mercure for real-time updates (planned)
* Full JWT authentication

This staged evolution ensures:

* incremental complexity
* controlled refactoring & backward compatibility
* clean separation between API and UI layers

## Technical Stack

### Backend

* **PHP 8.5**
* **Symfony 8.0**
* **API Platform 4.2** (REST, OpenAPI/Swagger, filters, pagination)
* Lexik JWT Authentication (token-based API auth)
* Doctrine ORM (with custom types for value objects)
* PostgreSQL
* Redis
* FrankenPHP

### Current Frontend

* Twig (server-side rendering)
* Symfony UX (Stimulus, Turbo, Components)
* Tailwind CSS
* Webpack Encore

### Target Frontend

* React SPA
* TypeScript
* TanStack Query (data fetching)

### Infrastructure

* Docker (reproducible environment)
* MinIO (object storage)
* Mailpit (email testing)
* Castor (task automation)

### Code Quality & Tooling

* PHPStan (static analysis)
* Rector (automated refactoring)
* PHP-CS-Fixer (code formatting)
* PHPCS (code standards)
* Twig CS Fixer (Twig template formatting)
* PHPUnit (with Paratest for parallel execution)
* DAMA Doctrine Test Bundle (test isolation)
* Symfony linters

Focus: type safety, static analysis, automated refactoring, reproducibility.

## Development Workflow

The project emphasizes:

* Dockerized reproducible environment
* Automated quality pipeline
* Structured Castor task orchestration
* Clear separation between infra / domain / UI

### Quick Start

```bash
git clone https://github.com/RomMad/surf-trip
cd surf-trip
docker compose up -d --build
docker compose exec php composer install
docker compose exec php symfony console doctrine:migrations:migrate
yarn install
yarn dev
```

### Local URLs

* App (HTTPS): https://localhost
* App (HTTP): http://localhost:8080
* **API Platform (Swagger UI)**: https://localhost/api/docs
* Mercure hub: https://localhost/.well-known/mercure
* pgAdmin: http://localhost:5050
* Mailpit (web UI): http://localhost:8025
* MinIO API: http://localhost:9000
* MinIO Console: http://localhost:9001
* Redis: redis://localhost:6379

## Quality Pipeline

```bash
castor quality
castor tests
castor tests-coverage
```

The goal is to maintain a production-grade codebase even during MVP evolution.

## Roadmap

### Short Term (In Progress)

* ✓ Define granular access control rules (security voters)
* ✓ Implement value objects
* ✓ Integrate API Platform for `Trip` and `User`
* Finalize React frontend (list / detail / create / edit flows)
* Migrate web UI to consume API Platform endpoints

### Mid Term

* Complete React SPA migration
* JWT-only authentication (remove session fallback)
* Real-time trip participation updates (Mercure)
* Extend API with more resources (Messages, Notifications, Reviews)
* API versioning strategy

### Long Term

* Messaging system
* Trip recommendations
* Multi-destination journeys
* Role-based collaboration (co-organizers)

## Engineering Principles

* Domain clarity before feature multiplication
* Incremental architecture evolution
* Tooling discipline (static analysis first)
* API-first thinking
* Clean migration path from monolith to SPA

## Project Status

**Active MVP evolution** with stable Symfony + API Platform foundation.

Architecture is production-ready for API-first development and incremental SPA migration.
