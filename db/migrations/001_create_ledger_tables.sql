-- Money is stored as integer minor units (ngwee, cents). See docs/adr/0002.
CREATE TABLE accounts (
    id            uuid PRIMARY KEY,
    currency      char(3)     NOT NULL,
    balance_minor bigint      NOT NULL DEFAULT 0 CHECK (balance_minor >= 0),
    created_at    timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE transfers (
    id              uuid PRIMARY KEY,
    idempotency_key varchar(100) NOT NULL UNIQUE,
    from_account_id uuid        NOT NULL REFERENCES accounts (id),
    to_account_id   uuid        NOT NULL REFERENCES accounts (id),
    amount_minor    bigint      NOT NULL CHECK (amount_minor > 0),
    currency        char(3)     NOT NULL,
    posted_at       timestamptz NOT NULL,
    CHECK (from_account_id <> to_account_id)
);

CREATE INDEX transfers_from_idx ON transfers (from_account_id, posted_at);
CREATE INDEX transfers_to_idx   ON transfers (to_account_id, posted_at);
