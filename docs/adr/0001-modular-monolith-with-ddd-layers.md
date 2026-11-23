# ADR 0001: Modular monolith with DDD layers

Status: accepted
Date: 2026-11-18

## Context
A finance API needs strict rules (no negative balances, no double posting) that must stay correct as the code grows. A team of one should not run several services.

## Decision
One deployable application split into bounded contexts, each with Domain, Application and Infrastructure layers. Domain depends on nothing. Application owns the interfaces that Infrastructure implements.

## Consequences
Business rules are testable without a database or a web server. Swapping Eloquent or Slim stays possible. The cost is more files and some boilerplate, which is accepted.
