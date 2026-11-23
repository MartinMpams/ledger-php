<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Ledger\Application\Port\TransferRepository;
use App\Ledger\Domain\IdempotencyKey;
use App\Ledger\Domain\Transfer;

final class InMemoryTransferRepository implements TransferRepository
{
    /** @var array<string, Transfer> */
    private array $byKey = [];

    public function findByKey(IdempotencyKey $key): ?Transfer
    {
        return $this->byKey[$key->value] ?? null;
    }

    public function add(Transfer $transfer): void
    {
        $this->byKey[$transfer->idempotencyKey->value] = $transfer;
    }

    public function count(): int
    {
        return count($this->byKey);
    }
}
