# Learning roadmap

Each stage ends with a pull request on your own repository, reviewed against `.github/pull_request_template.md`. Commit small and often. Do one stage at a time.

| Stage | Build | You prove you can... |
|---|---|---|
| 0. Setup | `composer install`, `composer ci` green, push to GitHub, confirm CI runs | Use Composer, Git and GitHub Actions |
| 1. Own the domain | Read `src/Ledger`. Add a `TransferPosted` domain event and a test. Add an `AccountOpened` use case. Extend tests for edge cases | Model rules in code, write behaviour tests |
| 2. Persistence | Eloquent models as infrastructure only. `EloquentAccountRepository` using `lockForUpdate()`, `EloquentTransferRepository`, `DatabaseTransactionManager`. Integration tests against real PostgreSQL | Keep Eloquent out of the domain, handle locking and transactions |
| 3. HTTP with Slim | `POST /accounts`, `POST /transfers` (Idempotency-Key header), `GET /accounts/{id}`. Middleware for JSON errors, validation and request ids. Map domain exceptions to status codes | Build a clean API layer in PSR-7/PSR-15 |
| 4. Security | API key or JWT auth, rate limiting, audit log table, no sensitive data in logs | Treat a finance API as a security problem |
| 5. Quality gates | PHPStan to max level, PHPCS clean, coverage report, optional mutation testing (Infection) | Enforce quality automatically |
| 6. CI/CD and deployment | Dockerfile (multi-stage), image build in Actions, tagged releases, deploy to a small VPS with Docker Compose, run migrations on deploy, write a rollback note | Ship and operate a service |
| 7. Documentation and review | OpenAPI file, ADR for each decision, runbook, review a peer or your own PR with the checklist | Keep docs alive, lead code review |
| 8. Observability | Structured JSON logs, `/health` and `/ready`, basic metrics | Operate in production |
| 9. Grow the system | Second bounded context (reconciliation or payouts), outbox pattern, queue with Redis | Keep a modular monolith modular |

## Interview kit (do alongside)

- Bitwise operators and circular shift (rotate bits left and right) in plain PHP, with tests.
- Explain, out loud and in two minutes, why money is stored as integers and why accounts are locked in a fixed order.
- Be ready to say what you would change to handle ten thousand transfers a second.
