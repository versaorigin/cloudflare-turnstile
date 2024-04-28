<?php

namespace VersaOrigin\CloudflareTurnstile\Contracts;

use Illuminate\Support\Collection;

interface CloudflareTurnstileContract
{
    public function getUri(): string;

    public function getKey(): string;

    public function getSecret(): string;

    public function setUri(string $uri): self;

    public function setEnabled(bool $enabled): self;

    public function setKey(string $key): self;

    public function setSecret(string $secret): self;

    public function setErrorCodes(array $errorCodes): self;

    public function setErrors(array $errors): self;

    public function getErrorCodes(): array;

    public function getErrorCode(string $errorCode): string;

    public function getErrors(): Collection;

    public function getErrorMessage(): string;

    public function isEnabled(): bool;

    public function isDisabled(): bool;

    public function enable(): self;

    public function disable(): self;

    public function hasErrors(): bool;

    public function failed(): bool;

    public function successful(): bool;

    public function validate(string $token, string $ip): bool;
}
