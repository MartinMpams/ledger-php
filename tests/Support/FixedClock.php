<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Ledger\Application\Port\Clock;

final class FixedClock implements Clock
{
    private readonly \DateTimeImmutable $now;

    public function __construct(?\DateTimeImmutable $now = null)
    {
        $this->now = $now ?? new \DateTimeImmutable('2026-11-18T10:00:00Z');
    }

    public function now(): \DateTimeImmutable
    {
        return $this->now;
    }
}
