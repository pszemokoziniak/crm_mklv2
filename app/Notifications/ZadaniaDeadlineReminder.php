<?php

namespace App\Notifications;

use App\Models\Zadania;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class ZadaniaDeadlineReminder extends Notification
{
    use Queueable;

    protected $zadania;
    protected $daysUntil;

    public function __construct(Zadania $zadania, int $daysUntil)
    {
        $this->zadania = $zadania;
        $this->daysUntil = $daysUntil;
    }

    public function via($notifiable)
    {
        return ['database', WebPushChannel::class];
    }

    public function toArray($notifiable)
    {
        $message = $this->daysUntil === 0
            ? "Dzisiaj termin zadania: \"{$this->zadania->subject}\""
            : "Za {$this->daysUntil} dni termin zadania: \"{$this->zadania->subject}\"";

        return [
            'message' => $message,
            'url' => "/zadania/{$this->zadania->id}/edit",
            'zadania_id' => $this->zadania->id,
        ];
    }

    public function toWebPush($notifiable, $notification)
    {
        $body = $this->daysUntil === 0
            ? "Dzisiaj termin zadania: \"{$this->zadania->subject}\""
            : "Za {$this->daysUntil} dni termin zadania: \"{$this->zadania->subject}\"";

        return (new WebPushMessage)
            ->title('Przypomnienie o zadaniu')
            ->icon('/favicon.svg')
            ->body($body)
            ->action('Przejdź do zadania', "/zadania/{$this->zadania->id}/edit")
            ->data(['url' => "/zadania/{$this->zadania->id}/edit"]);
    }
}
