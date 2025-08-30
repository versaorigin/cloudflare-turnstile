<?php

namespace VersaOrigin\CloudflareTurnstile\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use VersaOrigin\CloudflareTurnstile\Facades\CloudflareTurnstile;

class CloudflareTurnstileMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (CloudflareTurnstile::isDisabled()) {
            return $next($request);
        }

        $token = $request->input('cf-turnstile-response');

        if (! $token) {
            return response()->json([
                'message' => 'Turnstile token is required.',
            ], 422);
        }

        // Check if this IP has been recently verified (rate limiting)
        $cacheKey = 'turnstile_verified_'.md5($request->ip().$token);

        if (Cache::has($cacheKey)) {
            return response()->json([
                'message' => 'This token has already been verified.',
            ], 429);
        }

        $valid = CloudflareTurnstile::validate($token, $request->ip());

        if (! $valid) {
            return response()->json([
                'message' => CloudflareTurnstile::getErrorMessage() ?: 'Turnstile validation failed.',
            ], 422);
        }

        // Cache the successful validation for 5 minutes to prevent replay attacks
        Cache::put($cacheKey, true, now()->addMinutes(5));

        return $next($request);
    }
}
