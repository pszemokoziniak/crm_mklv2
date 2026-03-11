<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ForgotPasswordController extends Controller
{
    /**
     * Display the password reset link request view.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Auth/ForgotPassword', [
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            $status = Password::broker()->sendResetLink(
                $request->only('email')
            );

            if ($status === Password::RESET_LINK_SENT) {
                return redirect()->route('login')->with('success', 'Link do resetu hasła został wysłany na Twój adres e-mail.');
            }

            return back()->withErrors(['email' => __($status)]);

        } catch (\Exception $e) {
            // Zapisujemy błąd w logach, aby administrator mógł go sprawdzić
            Log::error('Błąd wysyłki maila (reset hasła): ' . $e->getMessage());

            return back()->withErrors([
                'email' => 'Wystąpił problem techniczny podczas wysyłania wiadomości e-mail. Skontaktuj się z administratorem lub spróbuj później.'
            ]);
        }
    }
}
