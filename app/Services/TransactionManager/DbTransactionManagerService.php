<?php

namespace App\Services\TransactionManager;

use Illuminate\Support\Facades\DB;

class DbTransactionManagerService implements TransactionManagerServiceInterface
{
    /**
     * Execute a callback within a database transaction.
     *
     * @param callable $callback
     * @return mixed
     * @throws \Throwable
     */
    public function runInTransaction(callable $callback)
    {
        return DB::transaction($callback);
    }
} 