<?php

declare(strict_types=1);

namespace App\Ledger\Domain\Exception;

final class IdempotencyConflict extends DomainException
{
    public static function because(
        string $detail = 'The idempotency key was already used with a different request.'
    ): self
    {
        return new self($detail);
    }
}
