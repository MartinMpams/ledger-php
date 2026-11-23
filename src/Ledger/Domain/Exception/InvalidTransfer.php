<?php

declare(strict_types=1);

namespace App\Ledger\Domain\Exception;

final class InvalidTransfer extends DomainException
{
    public static function because(string $detail = 'The transfer is not valid.'): self
    {
        return new self($detail);
    }
}
