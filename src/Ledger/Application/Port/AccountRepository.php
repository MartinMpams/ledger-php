<?php

declare(strict_types=1);

namespace App\Ledger\Application\Port;

use App\Ledger\Domain\Account;
use App\Ledger\Domain\AccountId;

interface AccountRepository
{
    /** Load an account and lock its row for the rest of the transaction (SELECT ... FOR UPDATE). */
    public function getForUpdate(AccountId $id): Account;

    public function save(Account $account): void;
}
