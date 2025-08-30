<?php

namespace VersaOrigin\CloudflareTurnstile;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Validator;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use VersaOrigin\CloudflareTurnstile\Contracts\CloudflareTurnstileContract;
use VersaOrigin\CloudflareTurnstile\Rules\CloudflareTurnstileRule;

class CloudflareTurnstileServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('cloudflare-turnstile')
            ->hasConfigFile()
            ->hasInstallCommand(function (InstallCommand $command) {
                $command->startWith(
                    function (InstallCommand $command) {
                        $command->info('Starting the installation process...');
                    }
                )
                    ->publishConfigFile()
                    ->copyAndRegisterServiceProviderInApp()
                    ->askToStarRepoOnGitHub('versaorigin/cloudflare-turnstile')
                    ->endWith(function (InstallCommand $command) {
                        $command->info('Installation complete.');
                    });
            });
    }

    public function packageRegistered()
    {
        $this->app->bind(CloudflareTurnstileContract::class, function ($app) {
            $config = $app['config']['cloudflare-turnstile'] ?? [];

            return new CloudflareTurnstile($config);
        });

        $this->app->singleton(CloudflareTurnstile::class, function ($app) {
            return $app->make(CloudflareTurnstileContract::class);
        });

        $this->app->alias(CloudflareTurnstileContract::class, 'cloudflare-turnstile');
    }

    public function packageBooted()
    {
        Validator::extend('cloudflare_turnstile', function (
            string $attribute,
            mixed $value,
            array $parameters,
            $validator
        ) {
            $rule = new CloudflareTurnstileRule;

            $rule->validate(
                $attribute,
                $value,
                fn (string $error) => $validator->errors()->add($attribute, $error)
            );

            return ! $validator->errors()->has($attribute);
        });

        // Add Blade directive for easy integration
        Blade::directive('turnstile', function ($expression) {
            $siteKey = config('cloudflare-turnstile.key');

            return <<<HTML
                <div class="cf-turnstile" data-sitekey="{$siteKey}" <?= $expression ?>></div>
                <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
            HTML;
        });
    }
}
