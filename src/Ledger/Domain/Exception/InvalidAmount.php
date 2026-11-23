<?php

declare(strict_types=1);

namespace App\Ledger\Domain\Exception;

final class InvalidAmount extends DomainException
{
    public static function because(string $detail = 'The amount is not valid.'): self
    {
        return new self($detail);
    }
}
