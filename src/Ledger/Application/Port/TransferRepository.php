<?php

declare(strict_types=1);

namespace App\Ledger\Application\Port;

use App\Ledger\Domain\IdempotencyKey;
use App\Ledger\Domain\Transfer;

interface TransferRepository
{
    public function findByKey(IdempotencyKey $key): ?Transfer;

    public function add(Transfer $transfer): void;
}
