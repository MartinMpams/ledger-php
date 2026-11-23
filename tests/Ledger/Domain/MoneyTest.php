<?php

declare(strict_types=1);

namespace Tests\Ledger\Domain;

use App\Ledger\Domain\Exception\CurrencyMismatch;
use App\Ledger\Domain\Exception\InvalidAmount;
use App\Ledger\Domain\Money;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    public function testAddsAndSubtractsInMinorUnits(): void
    {
        $total = Money::of(1050, 'ZMW')->add(Money::of(250, 'ZMW'));

        self::assertSame(1300, $total->amountMinor);
        self::assertSame(1050, $total->subtract(Money::of(250, 'ZMW'))->amountMinor);
    }

    public function testRefusesToMixCurrencies(): void
    {
        $this->expectException(CurrencyMismatch::class);
        Money::of(100, 'ZMW')->add(Money::of(100, 'USD'));
    }

    public function testRejectsBadCurrencyCode(): void
    {
        $this->expectException(InvalidAmount::class);
        Money::of(100, 'kwacha');
    }

    public function testRejectsOverflow(): void
    {
        $this->expectException(InvalidAmount::class);
        Money::of(PHP_INT_MAX, 'ZMW')->add(Money::of(1, 'ZMW'));
    }

    public function testEqualityNeedsSameAmountAndCurrency(): void
    {
        self::assertTrue(Money::of(5, 'ZMW')->equals(Money::of(5, 'ZMW')));
        self::assertFalse(Money::of(5, 'ZMW')->equals(Money::of(5, 'USD')));
    }
}
