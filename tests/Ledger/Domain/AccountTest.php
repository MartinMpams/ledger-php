<?php

declare(strict_types=1);

namespace Tests\Ledger\Domain;

use App\Ledger\Domain\Account;
use App\Ledger\Domain\AccountId;
use App\Ledger\Domain\Exception\InsufficientFunds;
use App\Ledger\Domain\Exception\InvalidAmount;
use App\Ledger\Domain\Money;
use PHPUnit\Framework\TestCase;

final class AccountTest extends TestCase
{
    public function testNewAccountStartsAtZero(): void
    {
        $account = Account::open(AccountId::generate(), 'ZMW');

        self::assertSame(0, $account->balance()->amountMinor);
    }

    public function testCreditThenDebit(): void
    {
        $account = Account::open(AccountId::generate(), 'ZMW');
        $account->credit(Money::of(1000, 'ZMW'));
        $account->debit(Money::of(400, 'ZMW'));

        self::assertSame(600, $account->balance()->amountMinor);
    }

    public function testCannotGoBelowZero(): void
    {
        $account = Account::open(AccountId::generate(), 'ZMW');
        $account->credit(Money::of(100, 'ZMW'));

        $this->expectException(InsufficientFunds::class);
        $account->debit(Money::of(101, 'ZMW'));
    }

    public function testRejectsZeroAmount(): void
    {
        $account = Account::open(AccountId::generate(), 'ZMW');

        $this->expectException(InvalidAmount::class);
        $account->credit(Money::of(0, 'ZMW'));
    }
}
