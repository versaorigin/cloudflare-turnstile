<?php

namespace VersaOrigin\CloudflareTurnstile\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use VersaOrigin\CloudflareTurnstile\CloudflareTurnstileServiceProvider;

class TestCase extends OrchestraTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            CloudflareTurnstileServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
