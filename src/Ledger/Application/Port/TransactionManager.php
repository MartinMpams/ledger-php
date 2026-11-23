<?php

declare(strict_types=1);

namespace App\Ledger\Application\Port;

interface TransactionManager
{
    /**
     * Run the callback in one database transaction. Commit on return, roll back on exception.
     *
     * @template T
     * @param callable(): T $work
     * @return T
     */
    public function run(callable $work): mixed;
}
