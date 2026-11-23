<?php

declare(strict_types=1);

namespace App\Ledger\Domain\Exception;

final class AccountNotFound extends DomainException
{
    public static function because(string $detail = 'The account was not found.'): self
    {
        return new self($detail);
    }
}
