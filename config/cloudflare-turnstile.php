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

];
