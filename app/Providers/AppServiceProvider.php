<?php

namespace App\Providers;

use App\Services\ExchangeRequestService\ExchangeRequestService;
use App\Services\ExchangeRequestService\StoreExchangeRequestService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->bind(ExchangeRequestService::class, StoreExchangeRequestService::class);
    }
}
