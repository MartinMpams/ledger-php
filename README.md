# ledger-php

A small finance ledger API built the way a senior PHP engineer would build it: Domain-Driven Design, clean layers, tests first, CI on every push, documented decisions.

The goal is learning. The domain and application layers are written and tested. You build the rest, stage by stage, in `docs/ROADMAP.md`.

## What it does

Open accounts and move money between them. Every transfer carries an idempotency key, so a retry never moves money twice.

## Layers

```
src/
  Ledger/
    Domain/        Rules of the business. No framework, no database, no HTTP.
    Application/   Use cases (PostTransferHandler) and ports (interfaces).
  Shared/          Tiny helpers.
tests/             Mirrors src. Test doubles live in tests/Support.
db/migrations/     SQL for PostgreSQL.
docs/              Roadmap, architecture, decision records (ADRs).
```

Still to build (see the roadmap): `Infrastructure/` (Eloquent repositories, transaction manager), `Http/` (Slim routes, middleware), Docker image, deployment.

## Quick start

```
composer install
composer ci          # code style, static analysis, tests
docker compose up -d # PostgreSQL for the persistence stage
```

Requirements: PHP 8.3, Composer, Docker. On Windows use Laragon or WSL2.

## Dependency rule

Source code points inwards only: Http and Infrastructure may depend on Application, Application may depend on Domain, Domain depends on nothing. If a Domain file imports Slim or Eloquent, the design is broken.
