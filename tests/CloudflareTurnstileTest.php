<?php

use Illuminate\Support\Collection;
use VersaOrigin\CloudflareTurnstile\CloudflareTurnstile;
use VersaOrigin\CloudflareTurnstile\Exceptions\CloudflareTurnstileException;

it('can get the URI', function () {
    $client = Mockery::mock(CloudflareTurnstile::class);
    $client->shouldReceive('getUri')->andReturn('https://challenges.cloudflare.com/turnstile/v0/siteverify');

    expect($client->getUri())->toBe('https://challenges.cloudflare.com/turnstile/v0/siteverify');
});

it('can check if it is enabled', function () {
    $client = Mockery::mock(CloudflareTurnstile::class);
    $client->shouldReceive('isEnabled')->andReturn(true);

    expect($client->isEnabled())->toBeTrue();
});

it('can check if it is disabled', function () {
    $client = Mockery::mock(CloudflareTurnstile::class);
    $client->shouldReceive('isDisabled')->andReturn(false);

    expect($client->isDisabled())->toBeFalse();
});

it('can set enabled', function () {
    $client = Mockery::mock(CloudflareTurnstile::class);
    $client->shouldReceive('setEnabled')->with(true)->andReturn($client);
    $client->shouldReceive('isEnabled')->andReturn(true);

    $client->setEnabled(true);

    expect($client->isEnabled())->toBeTrue();
});

it('can set disabled', function () {
    $client = Mockery::mock(CloudflareTurnstile::class);
    $client->shouldReceive('setDisabled')->with(false)->andReturn($client);
    $client->shouldReceive('isDisabled')->andReturn(false);

    $client->setDisabled(false);

    expect($client->isDisabled())->toBeFalse();
});

it('can get the key', function () {
    $client = Mockery::mock(CloudflareTurnstile::class);
    $client->shouldReceive('getKey')->andReturn('key');

    expect($client->getKey())->toBe('key');
});

it('can get the secret', function () {
    $client = Mockery::mock(CloudflareTurnstile::class);
    $client->shouldReceive('getSecret')->andReturn('secret');

    expect($client->getSecret())->toBe('secret');
});

it('can set the key', function () {
    $client = Mockery::mock(CloudflareTurnstile::class);
    $client->shouldReceive('setKey')->with('new-key')->andReturn($client);
    $client->shouldReceive('getKey')->andReturn('new-key');

    $client->setKey('new-key');

    expect($client->getKey())->toBe('new-key');
});

it('can set the secret', function () {
    $client = Mockery::mock(CloudflareTurnstile::class);
    $client->shouldReceive('setSecret')->with('new-secret')->andReturn($client);
    $client->shouldReceive('getSecret')->andReturn('new-secret');

    $client->setSecret('new-secret');

    expect($client->getSecret())->toBe('new-secret');
});

it('can set the error codes', function () {
    $errorCodes = [
        'unknown' => 'An unknown error happened while validating the response.',
        'disabled' => 'The captcha feature is disabled for this site.',
    ];

    $client = Mockery::mock(CloudflareTurnstile::class);
    $client->shouldReceive('setErrorCodes')->with($errorCodes)->andReturn($client);
    $client->shouldReceive('getErrorCodes')->andReturn($errorCodes);

    $client->setErrorCodes($errorCodes);

    expect($client->getErrorCodes())->toBe($errorCodes);
});

it('can get the error code', function () {
    $errorCodes = [
        'unknown' => 'An unknown error happened while validating the response.',
        'disabled' => 'The captcha feature is disabled for this site.',
    ];

    $client = Mockery::mock(CloudflareTurnstile::class);
    $client->shouldReceive('setErrorCodes')->with($errorCodes)->andReturn($client);
    $client->shouldReceive('getErrorCode')->with('unknown')->andReturn('An unknown error happened while validating the response.');
    $client->shouldReceive('getErrorCode')->with('disabled')->andReturn('The captcha feature is disabled for this site.');
    $client->shouldReceive('getErrorCode')->with('invalid')->andReturn('');

    $client->setErrorCodes($errorCodes);

    expect($client->getErrorCode('unknown'))->toBe('An unknown error happened while validating the response.');
    expect($client->getErrorCode('disabled'))->toBe('The captcha feature is disabled for this site.');
    expect($client->getErrorCode('invalid'))->toBe('');
});

it('can check if it does not have errors', function () {
    $client = Mockery::mock(CloudflareTurnstile::class);
    $client->shouldReceive('hasErrors')->andReturnFalse();

    expect($client->hasErrors())->toBeFalse();
});

it('can check if it has errors', function () {
    $errors = ['unknown', 'disabled'];

    $client = Mockery::mock(CloudflareTurnstile::class);
    $client->shouldReceive('setErrors')->with($errors)->andReturn($client);
    $client->shouldReceive('hasErrors')->andReturnTrue();

    $client->setErrors($errors);

    expect($client->hasErrors())->toBeTrue();
});

it('can get the errors', function () {
    $errors = ['unknown', 'disabled'];

    $client = Mockery::mock(CloudflareTurnstile::class);
    $client->shouldReceive('setErrors')->with($errors)->andReturn($client);
    $client->shouldReceive('hasErrors')->andReturnTrue();
    $client->shouldReceive('getErrors')->andReturn(collect($errors));

    $client->setErrors($errors);

    expect($client->hasErrors())->toBeTrue();
    expect($client->getErrors())->toBeInstanceOf(Collection::class);
    expect($client->getErrors())->toContain('unknown');
    expect($client->getErrors())->toContain('disabled');
});

it('can get the error message', function () {
    $errorCodes = [
        'unknown' => 'An unknown error happened while validating the response.',
        'disabled' => 'The captcha feature is disabled for this site.',
    ];
    $errors = ['unknown', 'disabled'];

    $client = Mockery::mock(CloudflareTurnstile::class);
    $client->shouldReceive('setErrorCodes')->with($errorCodes)->andReturn($client);
    $client->shouldReceive('setErrors')->with($errors)->andReturn($client);
    $client->shouldReceive('getErrorMessage')->andReturn('An unknown error happened while validating the response.');

    $client->setErrorCodes($errorCodes);
    $client->setErrors($errors);

    expect($client->getErrorMessage())->toBe('An unknown error happened while validating the response.');
});

it('can validate the token and IP', function () {
    $token = 'token';
    $ip = '127.0.0.1';

    $client = Mockery::mock(CloudflareTurnstile::class);
    $client->shouldReceive('validate')->with($token, $ip)->andReturnTrue();

    $response = $client->validate($token, $ip);

    expect($response)->toBeTrue();
});

it('throws an exception when validation fails', function () {
    $token = 'token';
    $ip = '127.0.0.1';

    $client = Mockery::mock(CloudflareTurnstile::class);
    $client->shouldReceive('validate')->with($token, $ip)->andThrow(
        CloudflareTurnstileException::class,
        'Failed to validate the response.'
    );

    $client->validate($token, $ip);
})->throws(CloudflareTurnstileException::class, 'Failed to validate the response.');

it('returns false when the response is not successful', function () {
    $token = 'token';
    $ip = '127.0.0.1';

    $client = Mockery::mock(CloudflareTurnstile::class);
    $client->shouldReceive('validate')->with($token, $ip)->andReturnFalse();
    $client->shouldReceive('hasErrors')->andReturnTrue();
    $client->shouldReceive('getErrors')->andReturn(collect(['unknown']));

    $response = $client->validate($token, $ip);

    expect($response)->toBeFalse();
    expect($client->hasErrors())->toBeTrue();
    expect($client->getErrors())->toBeInstanceOf(Collection::class);
    expect($client->getErrors())->toContain('unknown');
});

it('returns false when the response is empty', function () {
    $token = 'token';
    $ip = '127.0.0.1';

    $client = Mockery::mock(CloudflareTurnstile::class);
    $client->shouldReceive('validate')->with($token, $ip)->andReturnFalse();
    $client->shouldReceive('hasErrors')->andReturnTrue();
    $client->shouldReceive('getErrors')->andReturn(collect(['unknown']));

    $response = $client->validate($token, $ip);

    expect($response)->toBeFalse();
    expect($client->hasErrors())->toBeTrue();
    expect($client->getErrors())->toBeInstanceOf(Collection::class);
    expect($client->getErrors())->toContain('unknown');
});

it('returns false when there are error codes in the response', function () {
    $token = 'token';
    $ip = '127.0.0.1';

    $client = Mockery::mock(CloudflareTurnstile::class);
    $client->shouldReceive('validate')->with($token, $ip)->andReturnFalse();
    $client->shouldReceive('hasErrors')->andReturnTrue();
    $client->shouldReceive('getErrors')->andReturn(collect(['missing-input-secret', 'invalid-input-secret']));
    $client->shouldReceive('getErrorCodes')->andReturn([
        'missing-input-secret' => 'The secret parameter is missing.',
        'invalid-input-secret' => 'The secret parameter is invalid or malformed.',
    ]);

    $response = $client->validate($token, $ip);

    expect($response)->toBeFalse();
    expect($client->hasErrors())->toBeTrue();
    expect($client->getErrors())->toBeInstanceOf(Collection::class);
    expect($client->getErrors())->toContain('missing-input-secret');
    expect($client->getErrors())->toContain('invalid-input-secret');
});
