<?php

namespace VersaOrigin\CloudflareTurnstile;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Traits\Macroable;
use VersaOrigin\CloudflareTurnstile\Contracts\CloudflareTurnstileContract;
use VersaOrigin\CloudflareTurnstile\Exceptions\CloudflareTurnstileException;

class CloudflareTurnstile implements CloudflareTurnstileContract
{
    use Macroable;

    private array $errorCodes = [
        'unknown' => 'An unknown error happened while validating the response.',
        'disabled' => 'The captcha feature is disabled for this site.',
        'missing-input-secret' => 'The secret parameter was not passed.',
        'invalid-input-secret' => 'The secret parameter was invalid or did not exist.',
        'missing-input-response' => 'The response parameter was not passed.',
        'invalid-input-response' => 'The response parameter is invalid or has expired.',
        'invalid-widget-id' => 'The widget ID extracted from the parsed site secret key was invalid or did not exist.',
        'invalid-parsed-secret' => 'The secret extracted from the parsed site secret key was invalid.',
        'bad-request' => 'The request was rejected because it was malformed.',
        'timeout-or-duplicate' => 'The response parameter has already been validated before.',
        'internal-error' => 'An internal error happened while validating the response. The request can be retried.',
    ];

    private Collection $errors;

    private bool $enabled;

    private string $key;

    private string $secret;

    private string $uri = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    public function __construct(protected array $config)
    {
        $this->enabled = (bool) ($this->config['enabled'] ?? false);
        $this->key = $this->config['key'] ?? '';
        $this->secret = $this->config['secret'] ?? '';

        $this->errors = collect();
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getSecret(): string
    {
        return $this->secret;
    }

    public function setUri(string $uri): self
    {
        $this->uri = $uri;

        return $this;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function setSecret(string $secret): self
    {
        $this->secret = $secret;

        return $this;
    }

    public function setErrorCodes(array $errorCodes): self
    {
        $this->errorCodes = $errorCodes;

        return $this;
    }

    public function setErrors(array $errors): self
    {
        $this->errors = collect($errors);

        return $this;
    }

    public function getErrorCodes(): array
    {
        return $this->errorCodes;
    }

    public function getErrorCode(string $errorCode): string
    {
        return $this->errorCodes[$errorCode] ?? '';
    }

    public function getErrors(): Collection
    {
        return $this->errors;
    }

    public function getErrorMessage(): string
    {
        if ($errorCode = $this->getErrors()->first(
            fn ($errorCode) => array_key_exists($errorCode, $this->errorCodes)
        )) {
            return $this->getErrorCode($errorCode);
        }

        return '';
    }

    public function isEnabled(): bool
    {
        return (bool) $this->enabled;
    }

    public function isDisabled(): bool
    {
        return ! $this->isEnabled();
    }

    public function enable(): self
    {
        return $this->setEnabled(true);
    }

    public function disable(): self
    {
        return $this->setEnabled(false);
    }

    public function hasErrors(): bool
    {
        return $this->errors->isNotEmpty();
    }

    public function failed(): bool
    {
        return $this->hasErrors();
    }

    public function successful(): bool
    {
        return ! $this->hasErrors();
    }

    public function validate(string $token, string $ip): bool
    {
        if ($this->isDisabled()) {
            $this->setErrors(['disabled']);

            return true;
        }

        $response = Http::asJson()->timeout(30)->connectTimeout(10)
            ->post($this->getUri(), [
                'secret' => $this->getSecret(),
                'response' => $token,
                'remoteip' => $ip,
            ]);

        if (! $response) {
            throw new CloudflareTurnstileException('The response is empty.');
        }

        if ($response->failed()) {
            throw new CloudflareTurnstileException('Failed to validate the response.');
        }

        if (! $response->successful()) {
            $this->setErrors(['bad-request']);

            return false;
        }

        if ($response->collect()->isEmpty()) {
            $this->setErrors(['internal-error']);

            return false;
        }

        if ($response->collect()->has('error-codes') && $response->collect('error-codes')->isNotEmpty()) {
            $this->setErrors($response->json('error-codes', []));

            return false;
        }

        if ($response->collect()->has('success') && (bool) $response->json('success', false)) {
            return true;
        }

        $this->setErrors(['unknown']);

        return false;
    }
}
