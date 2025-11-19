<?php

namespace App\Providers;

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
        // Register route middleware aliases if router available
        if ($this->app->bound('router')) {
            $router = $this->app->make(\Illuminate\Routing\Router::class);
            // alias 'role' middleware to App\Http\Middleware\RoleMiddleware
            $router->aliasMiddleware('role', \App\Http\Middleware\RoleMiddleware::class);
        }
    }
}
