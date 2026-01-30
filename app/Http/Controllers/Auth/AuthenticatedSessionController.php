<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        // DEBUG: Logujemy co przychodzi
        Log::info('Próba logowania:', ['email' => $request->email]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            Log::warning('Użytkownik nie znaleziony w bazie:', ['email' => $request->email]);
            return Redirect::route('login')->withErrors(['email' => 'Nie ma takiego użytkownika.']);
        }

        if ($user->active === 0) {
            return Redirect::route('login')->withErrors(['email' => 'Konto zablokowane.']);
        }

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            Log::warning('Błędne hasło dla:', ['email' => $request->email]);
            return Redirect::route('login')->withErrors(['email' => 'Błędne hasło.']);
        }

        $request->session()->regenerate();

        $user->login_time = now();
        $user->save();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
