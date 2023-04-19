<?php

namespace App\Providers;

use App\Services\ExchangeRequestService\ExchangeRequestHandler;
use App\Services\ExchangeRequestService\ExchangeRequestService;
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
        $this->app->bind(ExchangeRequestService::class, ExchangeRequestHandler::class);
    }
}
