<?php

declare(strict_types=1);

namespace Tests\Ledger\Domain;

use App\Ledger\Domain\AccountId;
use App\Ledger\Domain\Exception\InvalidAmount;
use App\Ledger\Domain\Exception\InvalidTransfer;
use App\Ledger\Domain\IdempotencyKey;
use App\Ledger\Domain\Money;
use App\Ledger\Domain\Transfer;
use App\Ledger\Domain\TransferId;
use PHPUnit\Framework\TestCase;

final class TransferTest extends TestCase
{
    public function testCannotTransferToSameAccount(): void
    {
        $id = AccountId::generate();

        $this->expectException(InvalidTransfer::class);
        Transfer::post(
            TransferId::generate(),
            IdempotencyKey::fromString('k1'),
            $id,
            $id,
            Money::of(10, 'ZMW'),
            new \DateTimeImmutable()
        );
    }

    public function testAmountMustBePositive(): void
    {
        $this->expectException(InvalidAmount::class);
        Transfer::post(
            TransferId::generate(),
            IdempotencyKey::fromString('k1'),
            AccountId::generate(),
            AccountId::generate(),
            Money::of(0, 'ZMW'),
            new \DateTimeImmutable()
        );
    }

    public function testMatchesTheSameRequestOnly(): void
    {
        $a = AccountId::generate();
        $b = AccountId::generate();
        $transfer = Transfer::post(
            TransferId::generate(),
            IdempotencyKey::fromString('k1'),
            $a,
            $b,
            Money::of(10, 'ZMW'),
            new \DateTimeImmutable()
        );

        self::assertTrue($transfer->matches($a, $b, Money::of(10, 'ZMW')));
        self::assertFalse($transfer->matches($a, $b, Money::of(11, 'ZMW')));
        self::assertFalse($transfer->matches($b, $a, Money::of(10, 'ZMW')));
    }
}
