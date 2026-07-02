<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TrackUserActivity
{
    /**
     * Aktualizuje last_seen_at zalogowanego uzytkownika, ale nie czesciej niz raz
     * na 60 sekund - zeby nie generowac zbednych UPDATE'ow przy kazdym requeście.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $lastSeen = $user->last_seen_at;

            if (!$lastSeen || now()->diffInSeconds($lastSeen) > 60) {
                // update bez triggerowania eventow zeby uniknac szumu w activity log
                DB::table('users')->where('id', $user->id)->update(['last_seen_at' => now()]);
            }
        }

        return $next($request);
    }
}
