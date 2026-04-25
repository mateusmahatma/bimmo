<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->make('router')->pushMiddlewareToGroup('web', \App\Http\Middleware\SetLocale::class);
        $this->app->make('router')->pushMiddlewareToGroup('web', \App\Http\Middleware\InjectGlobalDangerAlerts::class);
    }
}
