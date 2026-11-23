<?php

declare(strict_types=1);

namespace App\Ledger\Domain;

use App\Shared\Uuid;

final readonly class TransferId
{
    private function __construct(public string $value)
    {
    }

    public static function generate(): self
    {
        return new self(Uuid::v4());
    }

    public static function fromString(string $value): self
    {
        if (!Uuid::isValid($value)) {
            throw new \InvalidArgumentException('Transfer id must be a UUID.');
        }

        return new self($value);
    }
}
