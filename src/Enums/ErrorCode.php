<?php

namespace VersaOrigin\CloudflareTurnstile\Enums;

enum ErrorCode: string
{
    case UNKNOWN = 'unknown';
    case DISABLED = 'disabled';
    case MISSING_INPUT_SECRET = 'missing-input-secret';
    case INVALID_INPUT_SECRET = 'invalid-input-secret';
    case MISSING_INPUT_RESPONSE = 'missing-input-response';
    case INVALID_INPUT_RESPONSE = 'invalid-input-response';
    case INVALID_WIDGET_ID = 'invalid-widget-id';
    case INVALID_PARSED_SECRET = 'invalid-parsed-secret';
    case BAD_REQUEST = 'bad-request';
    case TIMEOUT_OR_DUPLICATE = 'timeout-or-duplicate';
    case INTERNAL_ERROR = 'internal-error';

    public function message(): string
    {
        return match ($this) {
            self::UNKNOWN => 'An unknown error happened while validating the response.',
            self::DISABLED => 'The captcha feature is disabled for this site.',
            self::MISSING_INPUT_SECRET => 'The secret parameter was not passed.',
            self::INVALID_INPUT_SECRET => 'The secret parameter was invalid or did not exist.',
            self::MISSING_INPUT_RESPONSE => 'The response parameter was not passed.',
            self::INVALID_INPUT_RESPONSE => 'The response parameter is invalid or has expired.',
            self::INVALID_WIDGET_ID => 'The widget ID extracted from the parsed site secret key was invalid or did not exist.',
            self::INVALID_PARSED_SECRET => 'The secret extracted from the parsed site secret key was invalid.',
            self::BAD_REQUEST => 'The request was rejected because it was malformed.',
            self::TIMEOUT_OR_DUPLICATE => 'The response parameter has already been validated before.',
            self::INTERNAL_ERROR => 'An internal error happened while validating the response. The request can be retried.',
        };
    }
}
