<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class ImpersonationController extends Controller
{
    /**
     * Zaloguj sie jako inny user. Tylko super-admin / Administrator.
     * Oryginalny user zapamietany w sesji, mozna wrocic /impersonate/stop.
     */
    public function start(User $user)
    {
        $current = Auth::user();
        if (!$current || !$current->hasRole('super-admin')) {
            return Redirect::back()->with('error', 'Brak uprawnien do impersonacji.');
        }

        if ($current->id === $user->id) {
            return Redirect::back()->with('error', 'Juz jesteś tym uzytkownikiem.');
        }

        // Zapamietaj oryginalnego admina - zeby mozna bylo wrocic
        session(['impersonator_id' => $current->id]);

        Auth::login($user);

        return Redirect::route('dashboard')->with('success', "Zalogowano jako {$user->first_name} {$user->last_name}.");
    }

    public function stop()
    {
        $impersonatorId = session('impersonator_id');
        if (!$impersonatorId) {
            return Redirect::route('dashboard');
        }

        $impersonator = User::find($impersonatorId);
        if (!$impersonator) {
            session()->forget('impersonator_id');
            return Redirect::route('dashboard');
        }

        session()->forget('impersonator_id');
        Auth::login($impersonator);

        return Redirect::route('dashboard')->with('success', 'Wracasz do swojego konta.');
    }
}
