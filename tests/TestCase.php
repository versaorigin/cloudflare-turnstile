<?php

namespace VersaOrigin\CloudflareTurnstile\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use VersaOrigin\CloudflareTurnstile\CloudflareTurnstileServiceProvider;

class TestCase extends Orchestra
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
        config()->set('database.default', 'testing');
    }
}
