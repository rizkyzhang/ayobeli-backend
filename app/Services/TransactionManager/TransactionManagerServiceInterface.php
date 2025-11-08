<?php

namespace App\Services\TransactionManager;

interface TransactionManagerServiceInterface
{
    /**
     * Execute a callback within a database transaction.
     *
     * @param callable $callback
     * @return mixed
     * @throws \Throwable
     */
    public function runInTransaction(callable $callback);
} 