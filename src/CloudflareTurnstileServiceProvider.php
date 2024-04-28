<?php

namespace VersaOrigin\CloudflareTurnstile;

use Illuminate\Support\Facades\Validator;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use VersaOrigin\CloudflareTurnstile\Contracts\CloudflareTurnstileContract;
use VersaOrigin\CloudflareTurnstile\Rules\CloudflareTurnstileRule;

class CloudflareTurnstileServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('cloudflare-turnstile')
            ->hasConfigFile();
    }

    public function packageRegistered()
    {
        $this->app->singleton(CloudflareTurnstileContract::class, function ($app) {
            $config = $app['config']['cloudflare-turnstile'] ?? [];

            return new CloudflareTurnstile($config);
        });
    }

    public function packageBooted()
    {
        Validator::extend('cloudflare_turnstile', function (
            string $attribute,
            mixed $value,
            array $parameters,
            $validator
        ) {
            $rule = new CloudflareTurnstileRule();

            $rule->validate(
                $attribute,
                $value,
                fn (string $error) => $validator->errors()->add($attribute, $error)
            );

            return ! $validator->errors()->has($attribute);
        });
    }
}
