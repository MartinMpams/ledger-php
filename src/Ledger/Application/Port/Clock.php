<?php

declare(strict_types=1);

namespace App\Ledger\Application\Port;

interface Clock
{
    public function now(): \DateTimeImmutable;
}
