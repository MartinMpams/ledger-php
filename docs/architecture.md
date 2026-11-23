# Architecture

Style: modular monolith with DDD layers. One deployable unit, clear internal boundaries.

## The one use case so far: PostTransfer

1. The handler validates raw input into value objects (`Money`, `AccountId`, `IdempotencyKey`).
2. Inside one transaction it looks up the idempotency key. A known key returns the original transfer (same request) or raises `IdempotencyConflict` (different request).
3. It builds the `Transfer` (cheap rules fail before any lock).
4. It locks both accounts in id order (ADR 0003), debits and credits, saves both, stores the transfer.
5. The transaction commits. Any exception rolls everything back.

## Ports and adapters

The Application layer owns interfaces (`AccountRepository`, `TransferRepository`, `TransactionManager`, `Clock`). Infrastructure implements them. Tests use in-memory versions from `tests/Support`.

## Not built yet

Eloquent adapters, Slim HTTP layer, authentication, Docker image, deployment, observability. See `docs/ROADMAP.md`.
