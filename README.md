# A Cloudflare Turnstile Validator for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/versaorigin/cloudflare-turnstile.svg?style=flat-square)](https://packagist.org/packages/versaorigin/cloudflare-turnstile)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/versaorigin/cloudflare-turnstile/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/versaorigin/cloudflare-turnstile/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/versaorigin/cloudflare-turnstile/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/versaorigin/cloudflare-turnstile/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/versaorigin/cloudflare-turnstile.svg?style=flat-square)](https://packagist.org/packages/versaorigin/cloudflare-turnstile)

This package provides a validator for Laravel to validate Cloudflare Turnstile responses. It is useful when you want to validate a reCAPTCHA response from a form.

## Requirements

- PHP 8.3 or higher
- Laravel 11.0 or higher
- Cloudflare Turnstile API key and secret

## Installation

You can install the package via composer:

```bash
composer require versaorigin/cloudflare-turnstile
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="cloudflare-turnstile-config"
```

or, you can publish the config file with:

```bash
php artisan cloudflare-turnstile:install
```

This is the contents of the published config file:

```php
return [
    'enabled' => env('CLOUDFLARE_TURNSTILE_ENABLED', true),

    'key' => env('CLOUDFLARE_TURNSTILE_KEY', ''),

    'secret' => env('CLOUDFLARE_TURNSTILE_SECRET', ''),

    'timeout' => env('CLOUDFLARE_TURNSTILE_TIMEOUT', 30),

    'connect_timeout' => env('CLOUDFLARE_TURNSTILE_CONNECT_TIMEOUT', 10),

    'retry' => [
        'times' => env('CLOUDFLARE_TURNSTILE_RETRY_TIMES', 3),
        'sleep' => env('CLOUDFLARE_TURNSTILE_RETRY_SLEEP', 1000),
    ],

    'cache' => [
        'enabled' => env('CLOUDFLARE_TURNSTILE_CACHE_ENABLED', true),
        'ttl' => env('CLOUDFLARE_TURNSTILE_CACHE_TTL', 300),
    ],
];
```

## Usage

### Basic Validation

```php
$request->validate([
    "cf-turnstile-response" => ["required", "string", "turnstile"],
]);
```

### Using the Validation Rule Class

```php
use VersaOrigin\CloudflareTurnstile\Rules\CloudflareTurnstileRule;

$request->validate([
    "cf-turnstile-response" => ["required", "string", new CloudflareTurnstileRule],
]);
```

### Blade Directive

Add the Turnstile widget to your forms easily:

```blade
<form method="POST" action="/submit">
    @csrf

    <!-- Your form fields -->

    @turnstile

    <button type="submit">Submit</button>
</form>
```

### Middleware Protection

Protect entire routes with the Turnstile middleware:

```php
use VersaOrigin\CloudflareTurnstile\Middleware\CloudflareTurnstileMiddleware;

Route::post('/api/protected', function () {
    // Your protected logic
})->middleware(CloudflareTurnstileMiddleware::class);
```

### Programmatic Validation

```php
use VersaOrigin\CloudflareTurnstile\Facades\CloudflareTurnstile;

$token = $request->input('cf-turnstile-response');
$ip = $request->ip();

if (CloudflareTurnstile::validate($token, $ip)) {
    // Valid response
} else {
    // Invalid response
    $errorMessage = CloudflareTurnstile::getErrorMessage();
}
```

### Configuration Options

- **Retry Logic**: Automatically retries failed requests with configurable attempts and delays
- **Caching**: Prevents token replay attacks by caching successful validations
- **Logging**: Failed validations are logged for debugging
- **Timeout Control**: Configure connection and request timeouts

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Adrian Mejias](https://github.com/adrianmejias)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
