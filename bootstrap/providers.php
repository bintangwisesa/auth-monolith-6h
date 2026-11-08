<?php

use App\Providers\AppServiceProvider;
use Modules\Auth\Providers\AuthServiceProvider;
use Modules\Orders\Providers\OrderServiceProvider;
use Modules\Products\Providers\ProductServiceProvider;

return [
    AppServiceProvider::class,
    OrderServiceProvider::class,
    AuthServiceProvider::class,
    ProductServiceProvider::class,
];
