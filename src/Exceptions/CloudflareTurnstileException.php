<?php

namespace VersaOrigin\CloudflareTurnstile\Exceptions;

use Exception;
use Illuminate\Support\Collection;

class CloudflareTurnstileException extends Exception
{
    protected Collection $errorCodes;

    public function __construct(string $message = '', int $code = 0, ?Exception $previous = null, array $errorCodes = [])
    {
        parent::__construct($message, $code, $previous);
        $this->errorCodes = collect($errorCodes);
    }

    public static function validationFailed(string $message, array $errorCodes = []): self
    {
        return new self($message, 422, null, $errorCodes);
    }

    public static function networkError(string $message, ?Exception $previous = null): self
    {
        return new self($message, 500, $previous);
    }

    public function getErrorCodes(): Collection
    {
        return $this->errorCodes;
    }

    public function hasErrorCode(string $code): bool
    {
        return $this->errorCodes->contains($code);
    }
}
