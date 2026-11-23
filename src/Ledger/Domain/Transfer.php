<?php

declare(strict_types=1);

namespace App\Ledger\Domain;

use App\Ledger\Domain\Exception\InvalidAmount;
use App\Ledger\Domain\Exception\InvalidTransfer;

/** Immutable record that money moved from one account to another. */
final readonly class Transfer
{
    private function __construct(
        public TransferId $id,
        public IdempotencyKey $idempotencyKey,
        public AccountId $from,
        public AccountId $to,
        public Money $amount,
        public \DateTimeImmutable $postedAt
    ) {
    }

    public static function post(
        TransferId $id,
        IdempotencyKey $key,
        AccountId $from,
        AccountId $to,
        Money $amount,
        \DateTimeImmutable $postedAt
    ): self {
        if ($from->equals($to)) {
            throw InvalidTransfer::because('Source and destination must be different accounts.');
        }
        if (!$amount->isPositive()) {
            throw InvalidAmount::because('Transfer amount must be greater than zero.');
        }

        return new self($id, $key, $from, $to, $amount, $postedAt);
    }

    /** True when a retry carries the same request as the original. */
    public function matches(AccountId $from, AccountId $to, Money $amount): bool
    {
        return $this->from->equals($from) && $this->to->equals($to) && $this->amount->equals($amount);
    }
}
