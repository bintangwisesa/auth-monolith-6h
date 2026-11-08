<?php

namespace Modules\Membership\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Membership\Services\MemberService;
use Modules\Shared\Contracts\MemberServiceInterface;

class MembershipServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(MemberServiceInterface::class, MemberService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Migrations');
        $this->loadRoutesFrom(__DIR__ . '/../routes.php');
    }
}