# Cloudflare Turnstile Laravel Package - AI Coding Guide

## Architecture Overview

This is a Laravel package that provides Cloudflare Turnstile CAPTCHA validation. The package follows Laravel's service provider pattern with these key components:

- **Core Service** (`src/CloudflareTurnstile.php`): Main validation logic with HTTP client, retry mechanisms, and error handling
- **Service Provider** (`src/CloudflareTurnstileServiceProvider.php`): Registers services, validation rules, and Blade directive
- **Validation Rule** (`src/Rules/CloudflareTurnstileRule.php`): Laravel validation rule implementation
- **Middleware** (`src/Middleware/CloudflareTurnstileMiddleware.php`): Route-level protection with caching for replay attack prevention
- **Contract** (`src/Contracts/CloudflareTurnstileContract.php`): Interface defining the service contract

## Key Patterns & Conventions

### Configuration-Driven Design
All features are configurable via `config/cloudflare-turnstile.php`. The service can be disabled entirely for testing with `enabled: false`, which bypasses all validation while returning success.

### Error Handling Strategy
- Uses predefined error codes mapped to user-friendly messages in `CloudflareTurnstile::ERROR_CODES`
- Implements retry logic with exponential backoff for network failures
- Logs HTTP failures while returning graceful error responses
- Uses Laravel's Collection for error management (`$this->errors`)

### Security Patterns
- **Token Replay Prevention**: Middleware uses cache keys (`turnstile_verified_`.md5($ip.$token)) with 5-minute TTL
- **Rate Limiting**: IP-based validation caching prevents abuse
- **Timeout Protection**: Separate `timeout` (30s) and `connect_timeout` (10s) configs

## Development Workflows

### Testing Stack
```bash
# Run tests (uses Pest PHP testing framework)
composer test

# Run with coverage
composer test-coverage

# Static analysis
composer analyse  # Uses PHPStan with larastan

# Code formatting
composer format   # Uses Laravel Pint

# Build package for development
composer build    # Prepares testbench environment
```

### Package Development Pattern
Uses `orchestra/testbench` for Laravel package testing. The `TestCase` class in `tests/TestCase.php` provides the base setup. Tests use Pest syntax with `it()` functions and heavy Mockery usage for HTTP client mocking.

### Service Registration Flow
1. `CloudflareTurnstileServiceProvider::packageRegistered()` binds the contract and singleton
2. `packageBooted()` registers the `cloudflare_turnstile` validator and `@turnstile` Blade directive
3. Config is injected into the main service class constructor

## Integration Points

### Laravel Validation Integration
- Custom validator: `'cloudflare_turnstile'`
- Rule class: `new CloudflareTurnstileRule`
- Both validate the `cf-turnstile-response` field name (Cloudflare's default)

### HTTP Client Configuration
Uses Laravel's HTTP facade with specific retry logic:
```php
Http::asJson()
    ->timeout($timeout)
    ->connectTimeout($connectTimeout)
    ->retry($maxRetries, $retryDelay, function ($exception, $request) {
        return $exception instanceof ConnectionException
            || ($exception instanceof RequestException && $exception->response->serverError());
    })
```

### Frontend Integration
The package provides a `@turnstile` Blade directive that renders:
- Cloudflare widget div with site key from config
- Automatic script loading from Cloudflare CDN

## Common Gotchas

- **Environment Variables**: All configs have `CLOUDFLARE_TURNSTILE_*` prefixes
- **IP Detection**: Middleware uses `$request->ip()` - ensure proper proxy configuration for real IPs
- **Cache Dependency**: Middleware requires Laravel's cache system for replay protection
- **Testing Mode**: Set `enabled: false` in config for local development to bypass validation
- **Error Messages**: Use `CloudflareTurnstile::getErrorMessage()` for user-friendly error display

## File Naming Conventions

- All classes use `CloudflareTurnstile` prefix
- Namespace follows PSR-4: `VersaOrigin\CloudflareTurnstile`
- Config file uses kebab-case: `cloudflare-turnstile.php`
- Tests mirror source structure and use Pest's `it()` syntax

## Key Dependencies

- `spatie/laravel-package-tools`: Package scaffolding and install commands
- `illuminate/http`: HTTP client for API calls
- `orchestra/testbench`: Laravel package testing environment
- `pestphp/pest`: Testing framework with Laravel plugin
