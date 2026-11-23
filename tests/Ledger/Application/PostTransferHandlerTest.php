<?php

declare(strict_types=1);

namespace Tests\Ledger\Application;

use App\Ledger\Application\PostTransfer\PostTransferCommand;
use App\Ledger\Application\PostTransfer\PostTransferHandler;
use App\Ledger\Domain\Account;
use App\Ledger\Domain\AccountId;
use App\Ledger\Domain\Exception\AccountNotFound;
use App\Ledger\Domain\Exception\CurrencyMismatch;
use App\Ledger\Domain\Exception\IdempotencyConflict;
use App\Ledger\Domain\Exception\InsufficientFunds;
use App\Ledger\Domain\Money;
use PHPUnit\Framework\TestCase;
use Tests\Support\FixedClock;
use Tests\Support\InMemoryAccountRepository;
use Tests\Support\InMemoryTransferRepository;
use Tests\Support\PassThroughTransactions;

final class PostTransferHandlerTest extends TestCase
{
    private InMemoryAccountRepository $accounts;
    private InMemoryTransferRepository $transfers;
    private PostTransferHandler $handler;
    private AccountId $alice;
    private AccountId $bob;

    protected function setUp(): void
    {
        $this->accounts = new InMemoryAccountRepository();
        $this->transfers = new InMemoryTransferRepository();
        $this->handler = new PostTransferHandler(
            $this->accounts,
            $this->transfers,
            new PassThroughTransactions(),
            new FixedClock()
        );

        $this->alice = AccountId::generate();
        $this->bob = AccountId::generate();
        $this->accounts->save(Account::reconstitute($this->alice, Money::of(10_000, 'ZMW')));
        $this->accounts->save(Account::reconstitute($this->bob, Money::of(0, 'ZMW')));
    }

    private function command(
        string $key,
        int $amount = 2_500,
        ?AccountId $from = null,
        ?AccountId $to = null,
        string $currency = 'ZMW'
    ): PostTransferCommand {
        return new PostTransferCommand(
            $key,
            ($from ?? $this->alice)->value,
            ($to ?? $this->bob)->value,
            $amount,
            $currency
        );
    }

    public function testMovesMoneyBetweenAccounts(): void
    {
        $this->handler->handle($this->command('k1'));

        self::assertSame(7_500, $this->accounts->getForUpdate($this->alice)->balance()->amountMinor);
        self::assertSame(2_500, $this->accounts->getForUpdate($this->bob)->balance()->amountMinor);
    }

    public function testRetryWithSameKeyDoesNotMoveMoneyTwice(): void
    {
        $first = $this->handler->handle($this->command('k1'));
        $second = $this->handler->handle($this->command('k1'));

        self::assertSame($first->id->value, $second->id->value);
        self::assertSame(7_500, $this->accounts->getForUpdate($this->alice)->balance()->amountMinor);
        self::assertSame(1, $this->transfers->count());
    }

    public function testSameKeyWithDifferentAmountIsAConflict(): void
    {
        $this->handler->handle($this->command('k1', 2_500));

        $this->expectException(IdempotencyConflict::class);
        $this->handler->handle($this->command('k1', 9_999));
    }

    public function testInsufficientFundsChangesNothing(): void
    {
        try {
            $this->handler->handle($this->command('k2', 50_000));
            self::fail('Expected InsufficientFunds');
        } catch (InsufficientFunds) {
            self::assertSame(10_000, $this->accounts->getForUpdate($this->alice)->balance()->amountMinor);
            self::assertSame(0, $this->transfers->count());
        }
    }

    public function testUnknownAccount(): void
    {
        $this->expectException(AccountNotFound::class);
        $this->handler->handle($this->command('k3', 100, null, AccountId::generate()));
    }

    public function testCurrencyMustMatchBothAccounts(): void
    {
        $this->expectException(CurrencyMismatch::class);
        $this->handler->handle($this->command('k4', 100, null, null, 'USD'));
    }

    public function testOppositeTransfersBothSucceed(): void
    {
        $this->handler->handle($this->command('k5', 1_000));
        $this->handler->handle($this->command('k6', 400, $this->bob, $this->alice));

        self::assertSame(9_400, $this->accounts->getForUpdate($this->alice)->balance()->amountMinor);
        self::assertSame(600, $this->accounts->getForUpdate($this->bob)->balance()->amountMinor);
    }
}
