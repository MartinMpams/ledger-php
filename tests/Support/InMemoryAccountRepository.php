<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Ledger\Application\Port\AccountRepository;
use App\Ledger\Domain\Account;
use App\Ledger\Domain\AccountId;
use App\Ledger\Domain\Exception\AccountNotFound;

final class InMemoryAccountRepository implements AccountRepository
{
    /** @var array<string, Account> */
    private array $accounts = [];

    public function getForUpdate(AccountId $id): Account
    {
        return $this->accounts[$id->value] ?? throw AccountNotFound::because();
    }

    public function save(Account $account): void
    {
        $this->accounts[$account->id()->value] = $account;
    }
}
