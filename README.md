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

    'key' => env('CLOUDFLARE_TURNSTILE_KEY', 'your-cloudflare-turnstile-key'),

    'secret' => env('CLOUDFLARE_TURNSTILE_SECRET', 'your-cloudflare-turnstile-secret'),

];
```

## Usage

```php
$request->validate([
    'cf-turnstile-response' => 'required|cloudflare_turnstile',
]);
```

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
