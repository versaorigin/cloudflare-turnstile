<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use VersaOrigin\CloudflareTurnstile\Facades\CloudflareTurnstile;

it('can disable turnstile', function () {
    CloudflareTurnstile::disable();

    expect(CloudflareTurnstile::isDisabled())->toBeTrue();
});

it('can enable turnstile', function () {
    CloudflareTurnstile::enable();

    expect(CloudflareTurnstile::isEnabled())->toBeTrue();
});

it('can validate with valid token', function () {
    Http::fake([
        CloudflareTurnstile::getUri() => Http::response(['success' => true], 200),
    ]);

    $validator = Validator::make([
        'token' => 'valid-token',
    ], [
        'token' => ['required', 'string', 'cloudflare_turnstile'],
    ]);

    $validated = $validator->validate();

    expect($validated)->toBe(['token' => 'valid-token']);

    Http::assertSent(function ($request) {
        return $request->url() === CloudflareTurnstile::getUri()
            && $request['secret'] === CloudflareTurnstile::getSecret()
            && $request['response'] === 'valid-token'
            && $request['remoteip'] === request()->ip();
    });
})->skip(function () {
    return CloudflareTurnstile::isDisabled();
}, 'The turnstile is disabled.');

it('can validate with invalid token response', function () {
    Http::fake([
        CloudflareTurnstile::getUri() => Http::response([
            'success' => false,
            'error-codes' => [
                'invalid-input-response',
            ],
        ], 200),
    ]);
    Validator::make([
        'token' => 'invalid-token-response',
    ], [
        'token' => ['required', 'string', 'cloudflare_turnstile'],
    ])->validate();
})->throws(ValidationException::class, 'The response parameter is invalid or has expired.')->skip(function () {
    return CloudflareTurnstile::isDisabled();
}, 'The turnstile is disabled.');

it('can validate with invalid token', function () {
    Http::fake([
        CloudflareTurnstile::getUri() => Http::response([
            'success' => false,
        ], 200),
    ]);

    Validator::make([
        'token' => 'invalid-token',
    ], [
        'token' => ['required', 'string', 'cloudflare_turnstile'],
    ])->validate();
})->throws(ValidationException::class)->skip(function () {
    return CloudflareTurnstile::isDisabled();
}, 'The turnstile is disabled.');

it('can validate with missing token', function () {
    Validator::make([], [
        'token' => ['required', 'string', 'cloudflare_turnstile'],
    ])->validate();
})->throws(ValidationException::class, 'The token field is required.')->skip(function () {
    return CloudflareTurnstile::isDisabled();
}, 'The turnstile is disabled.');

it('can validate with invalid token type', function () {
    Validator::make([
        'token' => 123,
    ], [
        'token' => ['required', 'string', 'cloudflare_turnstile'],
    ])->validate();
})->throws(ValidationException::class, 'The token field must be a string.')->skip(function () {
    return CloudflareTurnstile::isDisabled();
}, 'The turnstile is disabled.');

it('can validate with disabled turnstile', function () {
    CloudflareTurnstile::disable();

    $validator = Validator::make([
        'token' => 'valid-token',
    ], [
        'token' => ['required', 'string', 'cloudflare_turnstile'],
    ]);

    $validated = $validator->validate();

    expect($validated)->toBe(['token' => 'valid-token']);
    expect(CloudflareTurnstile::hasErrors())->toBeTrue();
    expect(CloudflareTurnstile::getErrors())->toContain('disabled');

    Http::assertNotSent(function ($request) {
        return $request->url() === CloudflareTurnstile::getUri()
            && $request['secret'] === CloudflareTurnstile::getSecret()
            && $request['response'] === 'valid-token'
            && $request['remoteip'] === request()->ip();
    });
});
