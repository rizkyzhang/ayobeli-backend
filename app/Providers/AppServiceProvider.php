<?php

namespace App\Providers;

use App\Services\IndonesiaAddress\IndonesiaAddressService;
use App\Services\IndonesiaAddress\IndonesiaAddressServiceInterface;
use App\Services\TransactionManager\DbTransactionManagerService;
use App\Services\TransactionManager\NoopTransactionManagerService;
use App\Services\TransactionManager\TransactionManagerServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(IndonesiaAddressServiceInterface::class, IndonesiaAddressService::class);

        // Transaction manager binding
        if (app()->environment('testing')) {
            $this->app->bind(TransactionManagerServiceInterface::class, NoopTransactionManagerService::class);
        } else {
            $this->app->bind(TransactionManagerServiceInterface::class, DbTransactionManagerService::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
