<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
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
        Route::macro('customRoleResource', function ($uri, $controller) {
            Route::post("{$uri}/remove/{role?}", "{$controller}@destroyRole")->name("{$uri}.remove");
            Route::resource($uri, $controller)->except(['create', 'show', 'edit', 'destroy']);
        });
    }
}
