<?php

namespace Tests\Support;

use Closure;
use Illuminate\Http\Request;

/**
 * Passthrough CSRF middleware for testing.
 * Replaces VerifyCsrfToken so tests don't need to send CSRF tokens.
 */
class NoCsrfMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        return $next($request);
    }
}
