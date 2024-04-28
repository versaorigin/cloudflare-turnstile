<?php

namespace VersaOrigin\CloudflareTurnstile\Facades;

use Illuminate\Support\Facades\Facade;
use VersaOrigin\CloudflareTurnstile\Contracts\CloudflareTurnstileContract;

/**
 * @method static string getUri()
 * @method static string getKey()
 * @method static string getSecret()
 * @method static self setUri(string $uri)
 * @method static self setEnabled(bool $enabled)
 * @method static self setKey(string $key)
 * @method static self setSecret(string $secret)
 * @method static self setErrorCodes(array $errorCodes)
 * @method static self setErrors(array $errors)
 * @method static array getErrorCodes()
 * @method static string getErrorCode(string $errorCode)
 * @method static \Illuminate\Support\Collection getErrors()
 * @method static string getErrorMessage()
 * @method static bool isEnabled()
 * @method static bool isDisabled()
 * @method static self enable()
 * @method static self disable()
 * @method static bool hasErrors()
 * @method static bool failed()
 * @method static bool successful()
 * @method static bool validate(string $token, string $ip)
 *
 * @see \VersaOrigin\CloudflareTurnstile\CloudflareTurnstile
 */
class CloudflareTurnstile extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return CloudflareTurnstileContract::class;
    }
}
