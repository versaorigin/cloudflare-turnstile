<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cloudflare Turnstile Enabled
    |--------------------------------------------------------------------------
    |
    | This value determines whether the Cloudflare Turnstile middleware is
    | enabled.
    |
    */

    'enabled' => env('CLOUDFLARE_TURNSTILE_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Cloudflare Turnstile API Key
    |--------------------------------------------------------------------------
    |
    | This value is the API key used to authenticate with the Cloudflare
    | Turnstile API.
    |
    */

    'key' => env('CLOUDFLARE_TURNSTILE_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Cloudflare Turnstile API Secret
    |--------------------------------------------------------------------------
    |
    | This value is the API secret used to authenticate with the Cloudflare
    | Turnstile API.
    |
    */

    'secret' => env('CLOUDFLARE_TURNSTILE_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout in seconds for the validation request to Cloudflare.
    |
    */

    'timeout' => env('CLOUDFLARE_TURNSTILE_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Connection Timeout
    |--------------------------------------------------------------------------
    |
    | The connection timeout in seconds for establishing connection to Cloudflare.
    |
    */

    'connect_timeout' => env('CLOUDFLARE_TURNSTILE_CONNECT_TIMEOUT', 10),

    /*
    |--------------------------------------------------------------------------
    | Retry Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for retrying failed requests.
    |
    */

    'retry' => [
        'times' => env('CLOUDFLARE_TURNSTILE_RETRY_TIMES', 3),
        'sleep' => env('CLOUDFLARE_TURNSTILE_RETRY_SLEEP', 1000), // milliseconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for caching successful validations.
    |
    */

    'cache' => [
        'enabled' => env('CLOUDFLARE_TURNSTILE_CACHE_ENABLED', true),
        'ttl' => env('CLOUDFLARE_TURNSTILE_CACHE_TTL', 300), // seconds
    ],

];
