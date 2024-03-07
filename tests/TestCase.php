<?php

namespace Scolmore\ZeroTrust\Tests;

use Illuminate\Support\Facades\Route;
use Scolmore\ZeroTrust\ZeroTrust;
use Scolmore\ZeroTrust\ZeroTrustServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setupEnvironment($this->app);
        $this->registerMiddleware();
        $this->setupRoutes();
    }

    protected function getPackageProviders($app): array
    {
        return [
            ZeroTrustServiceProvider::class,
        ];
    }

    protected function setupEnvironment($app): void
    {
        $app['config']->set('zerotrust', [
            'enabled' => true,
            'title' => 'Testing',
            'application_name' => 'Testing',
            'auto_login' => false,
            'session_key' => 'zero-trust',
        ]);

        $app['config']->set('zerotrust.directories', [
            [
                'name' => 'Test',
                'tenant_id' => '123',
                'client_id' => '456',
                'secret' => '789',
                'allowed_domains' => [],
            ],
        ]);

        $app['config']->set('app.key', 'base64:9DSVRr2IB6Yu21FC2dmG+duRhj51Az5xzMXuxWwXzmM=');
    }

    protected function registerMiddleware(): void
    {
        $this->app['router']->aliasMiddleware('zero-trust', ZeroTrust::class);
    }

    protected function setupRoutes(): void
    {
        Route::get('/', static function () {
            return 'Hello World!';
        });

        Route::get('/logged-in', [
            'middleware' => 'zero-trust', static function () {
                return 'logged in';
            },
        ]);
    }
}
