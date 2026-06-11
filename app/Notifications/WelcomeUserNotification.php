<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Wysylane do nowo utworzonego uzytkownika - wiadomosc powitalna z linkiem
 * do ustawienia haslo (oparta o ten sam mechanizm co reset hasla).
 */
class WelcomeUserNotification extends ResetPassword
{
    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $expireMinutes = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

        return (new MailMessage)
            ->subject('Witaj w MKL CRM - ustaw swoje hasło')
            ->greeting('Witaj, ' . ($notifiable->first_name ?? '') . '!')
            ->line('Twoje konto w systemie MKL CRM zostało utworzone przez administratora.')
            ->line('Aby zacząć z niego korzystać, ustaw swoje hasło klikając w przycisk poniżej:')
            ->action('Ustaw hasło', $url)
            ->line('Link będzie aktywny przez ' . $expireMinutes . ' minut. Jeśli wygaśnie, skontaktuj się z administratorem lub skorzystaj z opcji "Nie pamiętam hasła" na stronie logowania.')
            ->line('Po ustawieniu hasła zaloguj się na crm.mkl.pl')
            ->salutation('Pozdrawiamy, Zespół MKL CRM');
    }
}
