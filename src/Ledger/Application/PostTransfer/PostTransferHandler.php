<?php

declare(strict_types=1);

namespace App\Ledger\Application\PostTransfer;

use App\Ledger\Application\Port\AccountRepository;
use App\Ledger\Application\Port\Clock;
use App\Ledger\Application\Port\TransactionManager;
use App\Ledger\Application\Port\TransferRepository;
use App\Ledger\Domain\AccountId;
use App\Ledger\Domain\Exception\CurrencyMismatch;
use App\Ledger\Domain\Exception\IdempotencyConflict;
use App\Ledger\Domain\IdempotencyKey;
use App\Ledger\Domain\Money;
use App\Ledger\Domain\Transfer;
use App\Ledger\Domain\TransferId;

/**
 * Use case: move money between two accounts exactly once.
 * Retrying with the same key returns the original transfer and changes nothing.
 */
final class PostTransferHandler
{
    public function __construct(
        private readonly AccountRepository $accounts,
        private readonly TransferRepository $transfers,
        private readonly TransactionManager $transactions,
        private readonly Clock $clock
    ) {
    }

    public function handle(PostTransferCommand $command): Transfer
    {
        $key = IdempotencyKey::fromString($command->idempotencyKey);
        $from = AccountId::fromString($command->fromAccountId);
        $to = AccountId::fromString($command->toAccountId);
        $amount = Money::of($command->amountMinor, $command->currency);

        return $this->transactions->run(function () use ($key, $from, $to, $amount): Transfer {
            $existing = $this->transfers->findByKey($key);
            if ($existing !== null) {
                if (!$existing->matches($from, $to, $amount)) {
                    throw IdempotencyConflict::because();
                }

                return $existing;
            }

            // Build the transfer first so cheap rules fail before any lock is taken.
            $transfer = Transfer::post(TransferId::generate(), $key, $from, $to, $amount, $this->clock->now());

            // Lock accounts in a fixed order so two opposite transfers cannot deadlock.
            // See docs/adr/0003-lock-ordering.md
            $ids = [$from, $to];
            usort($ids, static fn (AccountId $a, AccountId $b): int => strcmp($a->value, $b->value));
            $loaded = [];
            foreach ($ids as $id) {
                $loaded[$id->value] = $this->accounts->getForUpdate($id);
            }

            $source = $loaded[$from->value];
            $destination = $loaded[$to->value];
            if ($source->currency() !== $amount->currency || $destination->currency() !== $amount->currency) {
                throw CurrencyMismatch::because('Both accounts must use the transfer currency.');
            }

            $source->debit($amount);
            $destination->credit($amount);

            $this->accounts->save($source);
            $this->accounts->save($destination);
            $this->transfers->add($transfer);

            return $transfer;
        });
    }
}
