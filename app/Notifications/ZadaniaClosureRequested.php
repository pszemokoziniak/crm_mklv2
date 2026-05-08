<?php

namespace App\Notifications;

use App\Models\Zadania;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class ZadaniaClosureRequested extends Notification
{
    use Queueable;

    protected $zadania;
    protected $requestedBy;

    public function __construct(Zadania $zadania, User $requestedBy)
    {
        $this->zadania = $zadania;
        $this->requestedBy = $requestedBy;
    }

    public function via($notifiable)
    {
        return ['mail', 'database', WebPushChannel::class];
    }

    public function toMail($notifiable)
    {
        $url = url("/zadania/{$this->zadania->id}/edit");

        return (new MailMessage)
            ->subject('Zgłoszenie zamknięcia zadania - MKL CRM')
            ->greeting('Witaj!')
            ->line("{$this->requestedBy->first_name} {$this->requestedBy->last_name} zgłosił(a) zadanie do zamknięcia:")
            ->line("**{$this->zadania->subject}**")
            ->action('Przejdź do zadania', $url)
            ->line('Możesz zatwierdzić lub odrzucić zamknięcie tego zadania.')
            ->salutation('Pozdrawiamy, Zespół MKL CRM');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "{$this->requestedBy->first_name} {$this->requestedBy->last_name} zgłosił(a) zadanie \"{$this->zadania->subject}\" do zamknięcia.",
            'url' => "/zadania/{$this->zadania->id}/edit",
            'zadania_id' => $this->zadania->id,
            'requested_by' => $this->requestedBy->id,
        ];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('Zgłoszenie zamknięcia zadania')
            ->icon('/favicon.svg')
            ->body("{$this->requestedBy->first_name} {$this->requestedBy->last_name} zgłosił(a) zadanie \"{$this->zadania->subject}\" do zamknięcia.")
            ->action('Przejdź do zadania', "/zadania/{$this->zadania->id}/edit")
            ->data(['url' => "/zadania/{$this->zadania->id}/edit"]);
    }
}
