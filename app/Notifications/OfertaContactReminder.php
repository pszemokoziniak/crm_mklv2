<?php

namespace App\Notifications;

use App\Models\Oferta;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class OfertaContactReminder extends Notification
{
    use Queueable;

    protected $oferta;
    protected $daysUntil;

    public function __construct(Oferta $oferta, int $daysUntil)
    {
        $this->oferta = $oferta;
        $this->daysUntil = $daysUntil;
    }

    public function via($notifiable)
    {
        return ['mail', 'database', WebPushChannel::class];
    }

    public function toMail($notifiable)
    {
        $url = url("/oferta/{$this->oferta->id}/edit");
        $projectName = $this->oferta->zapytania ? $this->oferta->zapytania->nazwa_projektu : 'Oferta #' . $this->oferta->id;
        $clientName = $this->oferta->client ? $this->oferta->client->nazwa : '';

        $line = $this->daysUntil === 0
            ? "Dzisiaj przypada termin kontaktu dla oferty:"
            : "Za {$this->daysUntil} dni przypada termin kontaktu dla oferty:";

        return (new MailMessage)
            ->subject('Przypomnienie o kontakcie - MKL CRM')
            ->greeting('Witaj!')
            ->line($line)
            ->line("**{$projectName}** ({$clientName})")
            ->line("Data kontaktu: **{$this->oferta->data_kontakt->format('Y-m-d')}**")
            ->action('Przejdź do oferty', $url)
            ->salutation('Pozdrawiamy, Zespół MKL CRM');
    }

    public function toArray($notifiable)
    {
        $projectName = $this->oferta->zapytania ? $this->oferta->zapytania->nazwa_projektu : 'Oferta #' . $this->oferta->id;

        $message = $this->daysUntil === 0
            ? "Dzisiaj termin kontaktu: \"{$projectName}\""
            : "Za {$this->daysUntil} dni termin kontaktu: \"{$projectName}\"";

        return [
            'message' => $message,
            'url' => "/oferta/{$this->oferta->id}/edit",
            'oferta_id' => $this->oferta->id,
        ];
    }

    public function toWebPush($notifiable, $notification)
    {
        $projectName = $this->oferta->zapytania ? $this->oferta->zapytania->nazwa_projektu : 'Oferta #' . $this->oferta->id;

        $body = $this->daysUntil === 0
            ? "Dzisiaj termin kontaktu: \"{$projectName}\""
            : "Za {$this->daysUntil} dni termin kontaktu: \"{$projectName}\"";

        return (new WebPushMessage)
            ->title('Przypomnienie o kontakcie')
            ->icon('/favicon.svg')
            ->body($body)
            ->action('Przejdź do oferty', "/oferta/{$this->oferta->id}/edit")
            ->data(['url' => "/oferta/{$this->oferta->id}/edit"]);
    }
}
