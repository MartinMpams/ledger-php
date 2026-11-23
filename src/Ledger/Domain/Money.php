<?php

declare(strict_types=1);

namespace App\Ledger\Domain;

use App\Ledger\Domain\Exception\CurrencyMismatch;
use App\Ledger\Domain\Exception\InvalidAmount;

/**
 * Value object. Amounts are integers in minor units (ngwee, cents), never floats.
 * See docs/adr/0002-money-as-integer-minor-units.md
 */
final readonly class Money
{
    private function __construct(
        public int $amountMinor,
        public string $currency
    ) {
    }

    public static function of(int $amountMinor, string $currency): self
    {
        if (preg_match('/^[A-Z]{3}$/', $currency) !== 1) {
            throw InvalidAmount::because('Currency must be a three letter ISO 4217 code.');
        }

        return new self($amountMinor, $currency);
    }

    public static function zero(string $currency): self
    {
        return self::of(0, $currency);
    }

    public function add(self $other): self
    {
        $this->assertSameCurrency($other);
        $sum = $this->amountMinor + $other->amountMinor;
        if (!is_int($sum)) {
            throw InvalidAmount::because('Amount overflow.');
        }

        return new self($sum, $this->currency);
    }

    public function subtract(self $other): self
    {
        $this->assertSameCurrency($other);
        $diff = $this->amountMinor - $other->amountMinor;
        if (!is_int($diff)) {
            throw InvalidAmount::because('Amount overflow.');
        }

        return new self($diff, $this->currency);
    }

    public function isPositive(): bool
    {
        return $this->amountMinor > 0;
    }

    public function isLessThan(self $other): bool
    {
        $this->assertSameCurrency($other);

        return $this->amountMinor < $other->amountMinor;
    }

    public function equals(self $other): bool
    {
        return $this->currency === $other->currency && $this->amountMinor === $other->amountMinor;
    }

    private function assertSameCurrency(self $other): void
    {
        if ($this->currency !== $other->currency) {
            throw CurrencyMismatch::because("Cannot combine {$this->currency} with {$other->currency}.");
        }
    }
}
