<?php

declare(strict_types=1);

namespace App\Ledger\Application\PostTransfer;

/** Plain data from the outside world (an HTTP request, a CLI call). Validation happens in the handler. */
final readonly class PostTransferCommand
{
    public function __construct(
        public string $idempotencyKey,
        public string $fromAccountId,
        public string $toAccountId,
        public int $amountMinor,
        public string $currency
    ) {
    }
}
