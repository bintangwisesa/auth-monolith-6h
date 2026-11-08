<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Shared\Contracts\ProductServiceInterface;
use Modules\Products\Services\ProductService;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            ProductServiceInterface::class,
            ProductService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(app_path('Modules/Auth/Migrations'));
    }
}
