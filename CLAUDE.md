# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel package that provides Cloudflare Turnstile CAPTCHA validation. The package integrates seamlessly with Laravel's validation system, providing multiple integration patterns (validation rules, middleware, Blade directives, and programmatic validation).

## Development Commands

### Testing
```bash
# Run all tests using Pest PHP
composer test
# Alias: ./vendor/bin/pest

# Run tests with coverage report
composer test-coverage

# Run a single test file
./vendor/bin/pest tests/CloudflareTurnstileTest.php

# Run a specific test by name
./vendor/bin/pest --filter "test_name"
```

### Code Quality
```bash
# Run PHPStan static analysis (level 5 with larastan)
composer analyse
# Alias: vendor/bin/phpstan analyse

# Auto-fix code style issues using Laravel Pint
composer format
# Alias: vendor/bin/pint
```

### Package Development
```bash
# Build the testbench workbench environment
composer build

# Start local development server (requires build first)
composer start
```

## Architecture

### Service Provider Pattern
The package follows Laravel's service provider pattern with registration happening in two phases:

1. **`packageRegistered()`**: Binds the `CloudflareTurnstileContract` interface to the concrete `CloudflareTurnstile` class as a singleton. Also registers the `cloudflare-turnstile` alias.

2. **`packageBooted()`**: Registers the `cloudflare_turnstile` validator extension and the `@turnstile` Blade directive.

### Core Components

- **`CloudflareTurnstile`** (src/CloudflareTurnstile.php): Main service class implementing validation logic with HTTP client, retry mechanisms, and error handling. Uses Laravel's Collection for error management.

- **`CloudflareTurnstileRule`** (src/Rules/CloudflareTurnstileRule.php): Laravel validation rule implementation that wraps the core service.

- **`CloudflareTurnstileMiddleware`** (src/Middleware/CloudflareTurnstileMiddleware.php): Route-level protection that validates the `cf-turnstile-response` field and implements token replay prevention using Laravel's cache system.

- **`CloudflareTurnstileContract`** (src/Contracts/CloudflareTurnstileContract.php): Interface defining the service contract for dependency injection.

### Configuration-Driven Design
All features are controlled via `config/cloudflare-turnstile.php`. The service can be completely disabled for testing by setting `enabled: false`, which bypasses validation while returning success (useful for local development).

### Error Handling Architecture
- Error codes are mapped to user-friendly messages in `CloudflareTurnstile::ERROR_CODES`
- HTTP client implements retry logic with exponential backoff for connection failures and 5xx errors
- Configurable retry attempts (`retry.times`) and delay (`retry.sleep` in milliseconds)
- Failed HTTP requests are logged via Laravel's Log facade
- Use `CloudflareTurnstile::getErrorMessage()` to retrieve the first user-friendly error message

### Security Implementation
- **Token Replay Prevention**: Middleware caches validated tokens using `turnstile_verified_` + md5(ip+token) as the cache key with a 5-minute TTL
- **Timeout Protection**: Separate `timeout` (default 30s) and `connect_timeout` (default 10s) configurations prevent hanging requests
- **IP-Based Validation**: Uses `$request->ip()` - ensure proper proxy/trusted proxy configuration for accurate IP detection

## Testing Patterns

### Test Setup
- All tests extend `TestCase` which extends `Orchestra\Testbench\TestCase`
- Tests use Pest PHP syntax with `it()` functions and `describe()` blocks
- HTTP responses are mocked using Laravel's `Http::fake()` facade
- In-memory SQLite database is used for testing (configured in TestCase::getEnvironmentSetUp)

### Test File Naming
Test files mirror the source structure with `Test` suffix (e.g., `CloudflareTurnstile.php` → `CloudflareTurnstileTest.php`)

## Integration Points

### Validation Integration
The package provides two validation approaches:
1. String-based validator: `'cf-turnstile-response' => 'required|string|cloudflare_turnstile'`
2. Rule class: `'cf-turnstile-response' => ['required', 'string', new CloudflareTurnstileRule]`

Both validate the `cf-turnstile-response` field name (Cloudflare's default form field name).

### Blade Directive
The `@turnstile` directive renders the Cloudflare widget div with the site key from config and loads the Cloudflare CDN script. It accepts optional HTML attributes as parameters.

### Middleware Usage
The middleware can be applied to routes for automatic validation. It expects the `cf-turnstile-response` field in the request and uses caching to prevent token replay attacks.

### HTTP Client Configuration
The validate() method uses Laravel's HTTP facade with:
- JSON request format
- Configurable timeout and connection timeout
- Automatic retry on connection exceptions and server errors (5xx)
- Posts to `https://challenges.cloudflare.com/turnstile/v0/siteverify`

## Environment Variables

All configuration uses the `CLOUDFLARE_TURNSTILE_*` prefix:
- `CLOUDFLARE_TURNSTILE_ENABLED`: Enable/disable validation (default: true)
- `CLOUDFLARE_TURNSTILE_KEY`: Site key from Cloudflare dashboard
- `CLOUDFLARE_TURNSTILE_SECRET`: Secret key from Cloudflare dashboard
- `CLOUDFLARE_TURNSTILE_TIMEOUT`: Request timeout in seconds (default: 30)
- `CLOUDFLARE_TURNSTILE_CONNECT_TIMEOUT`: Connection timeout in seconds (default: 10)
- `CLOUDFLARE_TURNSTILE_RETRY_TIMES`: Number of retry attempts (default: 3)
- `CLOUDFLARE_TURNSTILE_RETRY_SLEEP`: Delay between retries in milliseconds (default: 1000)
- `CLOUDFLARE_TURNSTILE_CACHE_ENABLED`: Enable token replay prevention (default: true)
- `CLOUDFLARE_TURNSTILE_CACHE_TTL`: Cache TTL in seconds (default: 300)

## Key Dependencies

- **spatie/laravel-package-tools**: Provides package scaffolding and the `php artisan cloudflare-turnstile:install` command
- **illuminate/contracts**: Laravel framework contracts (supports Laravel 10-12)
- **orchestra/testbench**: Laravel package testing environment
- **pestphp/pest**: Modern PHP testing framework with Laravel plugin
- **larastan/phpstan**: PHPStan integration for Laravel with level 5 analysis

## Naming Conventions

- All classes use `CloudflareTurnstile` prefix
- Namespace: `VersaOrigin\CloudflareTurnstile`
- Config file: `cloudflare-turnstile.php` (kebab-case)
- Validator name: `cloudflare_turnstile` (snake_case)
- Facade alias: `CloudflareTurnstile`
- Service alias: `cloudflare-turnstile`
