<?php

declare(strict_types=1);

namespace App\Ledger\Domain\Exception;

final class InsufficientFunds extends DomainException
{
    public static function because(string $detail = 'The account has insufficient funds.'): self
    {
        return new self($detail);
    }
}
