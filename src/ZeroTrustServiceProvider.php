<?php

declare(strict_types=1);

namespace Scolmore\ZeroTrust;

use Illuminate\Support\ServiceProvider;

class ZeroTrustServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }

        $this->loadViewsFrom(__DIR__.'/../src/views', 'zero-trust');
        $this->loadRoutesFrom(__DIR__.'/../src/Http/routes.php');

        app('router')->aliasMiddleware('zero-trust', ZeroTrust::class);
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/zerotrust.php', 'zerotrust');
    }

    protected function bootForConsole(): void
    {
        $this->publishes([
            __DIR__.'/../config/zerotrust.php' => config_path('zerotrust.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../src/views' => base_path('resources/views/vendor/zero-trust'),
        ], 'views');
    }
}
