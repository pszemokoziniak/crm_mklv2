<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Sso;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Przejście CRM → HRM i wejście z HRM → CRM (patrz App\Services\Sso).
 */
class SsoController extends Controller
{
    /** CRM → HRM: podpisz tożsamość i przekieruj do HRM. */
    public function doHrm(Sso $sso): RedirectResponse
    {
        $url = rtrim((string) config('services.sso.hrm_url'), '/');
        abort_if($url === '', 500, 'Brak adresu HRM (SSO_HRM_URL).');

        $token = $sso->podpisz((string) Auth::user()->email);

        return redirect()->away($url.'/sso/wejscie?token='.urlencode($token));
    }

    /** Wejście z HRM: sprawdź token i zaloguj użytkownika CRM po e-mailu. */
    public function wejscie(Sso $sso): RedirectResponse
    {
        $email = $sso->odczytaj(Request::input('token'));

        if (! $email) {
            return redirect('/login')->withErrors(['email' => 'Link przejścia wygasł lub jest nieprawidłowy. Zaloguj się.']);
        }

        $user = User::where('email', $email)->where('active', true)->whereNull('deleted_at')->first();

        if (! $user) {
            return redirect('/login')->withErrors(['email' => 'To konto nie istnieje w CRM albo jest nieaktywne. Skontaktuj się z administratorem.']);
        }

        Auth::login($user);
        Request::session()->regenerate();

        return redirect('/');
    }
}
