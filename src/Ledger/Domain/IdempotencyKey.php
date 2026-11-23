<?php

declare(strict_types=1);

namespace App\Ledger\Domain;

final readonly class IdempotencyKey
{
    private function __construct(public string $value)
    {
    }

    public static function fromString(string $value): self
    {
        $value = trim($value);
        if ($value === '' || strlen($value) > 100) {
            throw new \InvalidArgumentException('Idempotency key must be 1 to 100 characters.');
        }

        return new self($value);
    }
}
