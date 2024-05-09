<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use URL;

class ForceHttpsProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     * for k8s 版本使用
     *
     * @return void
     */
    public function boot()
    {
        if (config('app.force_use_https')) {
            URL::forceScheme('https');
        }
    }
}
