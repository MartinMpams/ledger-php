<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Ledger\Application\Port\TransactionManager;

/** Test double: no real database, so just run the work. */
final class PassThroughTransactions implements TransactionManager
{
    public function run(callable $work): mixed
    {
        return $work();
    }
}
