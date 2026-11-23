<?php

declare(strict_types=1);

namespace App\Ledger\Domain;

use App\Ledger\Domain\Exception\InsufficientFunds;
use App\Ledger\Domain\Exception\InvalidAmount;

/**
 * Aggregate root. All balance changes go through debit() and credit(),
 * so the "no negative balance" rule lives in exactly one place.
 */
final class Account
{
    private function __construct(
        private readonly AccountId $id,
        private Money $balance
    ) {
    }

    public static function open(AccountId $id, string $currency): self
    {
        return new self($id, Money::zero($currency));
    }

    /** Rebuild an account from storage. Skips the rules because the state was already valid. */
    public static function reconstitute(AccountId $id, Money $balance): self
    {
        return new self($id, $balance);
    }

    public function id(): AccountId
    {
        return $this->id;
    }

    public function balance(): Money
    {
        return $this->balance;
    }

    public function currency(): string
    {
        return $this->balance->currency;
    }

    public function credit(Money $amount): void
    {
        $this->assertPositive($amount);
        $this->balance = $this->balance->add($amount);
    }

    public function debit(Money $amount): void
    {
        $this->assertPositive($amount);
        if ($this->balance->isLessThan($amount)) {
            throw InsufficientFunds::because();
        }
        $this->balance = $this->balance->subtract($amount);
    }

    private function assertPositive(Money $amount): void
    {
        if (!$amount->isPositive()) {
            throw InvalidAmount::because('Amount must be greater than zero.');
        }
    }
}
