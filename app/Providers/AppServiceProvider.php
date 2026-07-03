<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ReferenceNumberService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ReferenceNumberService::class, function ($app) {
            return new ReferenceNumberService();
        });
    }

    public function boot(): void
    {
        //
    }
}
