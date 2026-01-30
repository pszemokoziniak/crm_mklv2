<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return Inertia::render('Auth/Login');
    }

    public function store(Request $request)
    {
        // To MUSI się pojawić w logach lub na ekranie
        dd($request->all());

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return Redirect::route('login')->withErrors(['email' => 'Nie ma takiego użytkownika.']);
        }

        if ($user->active === 0) {
            return Redirect::route('login')->withErrors(['email' => 'Konto zablokowane.']);
        }

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            return Redirect::route('login')->withErrors(['email' => 'Błędne hasło.']);
        }

        $request->session()->regenerate();

        $user->login_time = now();
        $user->save();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
