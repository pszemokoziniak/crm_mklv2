<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class EnsureProfileIsComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Sprawdzamy czy imię lub nazwisko jest puste lub zawiera "N/A"
            $isInvalid = empty($user->first_name) ||
                         empty($user->last_name) ||
                         trim(strtoupper($user->last_name)) === 'N/A' ||
                         trim(strtoupper($user->first_name)) === 'N/A';

            if ($isInvalid) {
                // Pozwól na dostęp do trasy uzupełniania profilu, zapisu oraz wylogowania
                $allowedRoutes = [
                    'profile.complete',
                    'profile.complete.store',
                    'logout',
                ];

                if (!$request->routeIs($allowedRoutes)) {
                    return Redirect::route('profile.complete');
                }
            }
        }

        return $next($request);
    }
}
