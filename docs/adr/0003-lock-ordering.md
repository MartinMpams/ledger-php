# ADR 0003: Lock accounts in a fixed order

Status: accepted
Date: 2026-11-18

## Context
Two transfers in opposite directions between the same accounts can each lock one account and wait for the other (deadlock).

## Decision
Within a transaction, lock both accounts with `SELECT ... FOR UPDATE` in ascending account id order, regardless of transfer direction.

## Consequences
Deadlock between the two accounts becomes impossible. Locks are held until commit, so keep the transaction short.
