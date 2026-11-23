<?php

declare(strict_types=1);

namespace App\Ledger\Domain\Exception;

final class CurrencyMismatch extends DomainException
{
    public static function because(string $detail = 'The currencies do not match.'): self
    {
        return new self($detail);
    }
}
