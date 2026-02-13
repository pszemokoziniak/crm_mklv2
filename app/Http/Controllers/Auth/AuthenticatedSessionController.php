<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Inertia\Response
     */
    public function create(Request $request)
    {
        // Używamy Cookie::get() dla pewności
        $rememberedEmail = $request->cookie('remembered_email') ?? Cookie::get('remembered_email');

        return Inertia::render('Auth/Login', [
            'status' => session('status'),
            'rememberedEmail' => $rememberedEmail,
        ]);
    }

    /**
     * Handle an incoming authentication request.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $user = User::withTrashed()->where('email', $request->email)->first();

        if (!$user || $user->trashed()) {
            return Redirect::route('login')->withErrors(['email' => 'Konto zostało usunięte lub nie istnieje.']);
        }

        if ($user->active === 0) {
            return Redirect::route('login')->withErrors(['email' => 'Konto zablokowane.']);
        }

        $request->authenticate();
        $request->session()->regenerate();

        // Zapamiętywanie adresu email w cookie na 30 dni
        if ($request->remember) {
            Cookie::queue('remembered_email', $request->email, 60 * 24 * 30);
        } else {
            Cookie::queue(Cookie::forget('remembered_email'));
        }

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
