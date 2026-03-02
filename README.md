# Surf Trip

Surf Trip is a web application designed to connect people who want to **organize** or **join** surf trips — from short getaways to multi-week surf journeys.

The project is built as an evolving MVP with a clear architectural trajectory:
**Symfony core → API Platform → React SPA.**

## Product Vision

Surf Trip addresses a simple but fragmented problem:

Surfers often coordinate trips through messaging apps, spreadsheets, or informal groups. There is no lightweight platform focused on:

* trip discovery by level and dates
* structured organization
* transparent participation
* scalable collaboration

The goal is to build a focused, extensible platform with a clean domain model and a modern API-first architecture.

## Core Features (Current MVP)

* User authentication
* Trip creation (location, dates, level requirements, description)
* Trip discovery
* Participation management
* Admin tools
* Fixtures for realistic development data

## Domain Model

The application is structured around a clear domain foundation:

* **User**
* **Trip**
* **TripParticipant**
* (Planned) Message / Discussion
* (Planned) Notification

The objective is to keep business logic encapsulated and progressively expose it through an API layer.

## Architecture Strategy

### Current Architecture (MVP)

* Symfony 7 (monolithic)
* Twig rendering
* Doctrine ORM
* Session-based authentication
* Modular UX components (Stimulus, Turbo, Twig Components)

### Target Architecture

* Symfony as API core
* API Platform for resource exposure
* JWT-based authentication
* React SPA consuming the API
* Mercure for real-time updates (planned)

This staged evolution ensures:

* incremental complexity
* controlled refactoring
* backward compatibility during transition

## Technical Stack

### Backend

* PHP 8.4+
* Symfony 7.4
* Doctrine ORM
* PostgreSQL
* Redis
* FrankenPHP
* API Platform (target integration)

### Frontend

* Twig (current)
* Symfony UX (Stimulus, Turbo, Components)
* Tailwind CSS
* Webpack Encore
* React (target integration)

### Infrastructure

* Docker
* MinIO
* Mailpit
* Castor (task automation)

### Code Quality & Tooling

* PHPStan
* Rector
* PHP-CS-Fixer
* PHPCS
* Twig CS Fixer
* PHPUnit
* Symfony linters

Focus: static analysis, automated refactoring, reproducibility.

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

### Short Term

* Introduce API Platform for `Trip` and `User`
* Define granular access control rules
* Expose filtered search endpoints

### Mid Term

* React front-end (list / detail / create / edit flows)
* Authentication migration to JWT
* Real-time trip participation updates

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

Evolving MVP with a stable Symfony foundation.
Architecture intentionally prepared for API-first expansion and SPA transition.
