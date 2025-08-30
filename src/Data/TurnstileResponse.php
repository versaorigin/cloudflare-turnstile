<?php

namespace VersaOrigin\CloudflareTurnstile\Data;

use Illuminate\Support\Collection;

readonly class TurnstileResponse
{
    public function __construct(
        public bool $success,
        public string $challengeTs,
        public string $hostname,
        public Collection $errorCodes,
        public string $action = '',
        public string $cdata = '',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            success: $data['success'] ?? false,
            challengeTs: $data['challenge_ts'] ?? '',
            hostname: $data['hostname'] ?? '',
            errorCodes: collect($data['error-codes'] ?? []),
            action: $data['action'] ?? '',
            cdata: $data['cdata'] ?? '',
        );
    }

    public function failed(): bool
    {
        return ! $this->success;
    }

    public function hasErrors(): bool
    {
        return $this->errorCodes->isNotEmpty();
    }
}
