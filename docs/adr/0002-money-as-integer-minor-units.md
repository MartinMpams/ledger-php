# ADR 0002: Money as integer minor units

Status: accepted
Date: 2026-11-18

## Context
Floating point numbers cannot represent most decimal amounts exactly. `0.1 + 0.2` is not `0.3`.

## Decision
Store and calculate money as integers in minor units (ngwee, cents) together with an ISO 4217 currency code, in the `Money` value object and in `bigint` database columns. Convert to decimals only at the edges (display, API formatting).

## Consequences
Exact arithmetic. Overflow must be checked, and `Money` does this. Mixed currencies are refused.
