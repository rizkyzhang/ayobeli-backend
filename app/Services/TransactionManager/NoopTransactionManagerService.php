<?php

namespace App\Services\TransactionManager;

class NoopTransactionManagerService implements TransactionManagerServiceInterface
{
    /**
     * Execute a callback without a transaction (for testing).
     *
     * @param callable $callback
     * @return mixed
     */
    public function runInTransaction(callable $callback)
    {
        return $callback();
    }
} 