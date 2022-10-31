<?php

namespace Mimocodes\Payment\Providers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;

class PaymentProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../views','payment');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        Artisan::call('php artisan make:model Callback',[
            '--class' => 'App\Models'
        ]);
    }
}
