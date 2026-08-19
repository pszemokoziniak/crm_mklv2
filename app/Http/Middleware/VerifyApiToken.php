<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Prosty bearer-token dla wewnętrznego API (np. HRM czytający klientów).
 * Token współdzielony, ustawiany w .env obu aplikacji (HRM_API_TOKEN).
 */
class VerifyApiToken
{
    public function handle(Request $request, Closure $next)
    {
        $expected = (string) config('services.hrm.token');
        $provided = (string) $request->bearerToken();

        if ($expected === '' || !hash_equals($expected, $provided)) {
            abort(401, 'Nieprawidłowy token API.');
        }

        return $next($request);
    }
}
