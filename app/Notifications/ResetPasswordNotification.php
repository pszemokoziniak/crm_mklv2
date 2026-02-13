<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Resetowanie hasła - MKL CRM')
            ->greeting('Witaj!')
            ->line('Otrzymujesz tę wiadomość, ponieważ otrzymaliśmy prośbę o zresetowanie hasła do Twojego konta.')
            ->action('Resetuj hasło', $url)
            ->line('Ten link do resetowania hasła wygaśnie za ' . config('auth.passwords.'.config('auth.defaults.passwords').'.expire') . ' minut.')
            ->line('Jeśli to nie Ty prosiłeś o resetowanie hasła, nie musisz podejmować żadnych dalszych działań.')
            ->salutation('Pozdrawiamy, Zespół MKL CRM');
    }
}
