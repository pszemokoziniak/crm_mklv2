<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
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
        // Zerujemy last_seen_at zanim wylogujemy - dzieki temu user
        // znika z listy 'online' od razu, a nie po 24h.
        $user = Auth::guard('web')->user();
        if ($user) {
            DB::table('users')->where('id', $user->id)->update(['last_seen_at' => null]);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function showCompleteProfile()
    {
        return Inertia::render('Auth/CompleteProfile', [
            'user' => Auth::user()->only('first_name', 'last_name', 'email'),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:50', 'min:2'],
            'last_name' => ['required', 'string', 'max:50', 'min:2'],
        ], [
            'first_name.required' => 'Pole imię jest wymagane.',
            'first_name.min' => 'Imię musi mieć co najmniej :min znaki.',
            'first_name.max' => 'Imię nie może być dłuższe niż :max znaków.',
            'last_name.required' => 'Pole nazwisko jest wymagane.',
            'last_name.min' => 'Nazwisko musi mieć co najmniej :min znaki.',
            'last_name.max' => 'Nazwisko nie może być dłuższe niż :max znaków.',
        ]);

        $user = Auth::user();
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
        ]);

        return redirect()->intended(RouteServiceProvider::HOME)->with('success', 'Dane zostały zaktualizowane.');
    }
}
