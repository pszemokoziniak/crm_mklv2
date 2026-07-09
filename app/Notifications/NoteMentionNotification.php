<?php

namespace App\Notifications;

use App\Models\Note;
use App\Models\User;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class NoteMentionNotification extends Notification
{
    protected Note $note;
    protected User $author;

    public function __construct(Note $note, User $author)
    {
        $this->note = $note;
        $this->author = $author;
    }

    public function via($notifiable)
    {
        $channels = ['database'];
        if (!empty($notifiable->email)) {
            $channels[] = WebPushChannel::class;
        }
        return $channels;
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "{$this->author->first_name} {$this->author->last_name} wspomniał Cię w notatce",
            'preview' => mb_substr($this->note->body, 0, 100),
            'url' => $this->urlFor(),
            'note_id' => $this->note->id,
            'notable_type' => class_basename($this->note->notable_type),
            'notable_id' => $this->note->notable_id,
        ];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title("💬 {$this->author->first_name} {$this->author->last_name} — nowa wzmianka")
            ->icon('/favicon.svg')
            ->body(mb_substr($this->note->body, 0, 100))
            ->data(['url' => $this->urlFor()])
            ->action('Zobacz', $this->urlFor());
    }

    protected function urlFor(): string
    {
        $type = class_basename($this->note->notable_type);
        return match ($type) {
            'Zapytania' => "/zapytania/{$this->note->notable_id}/edit",
            'Oferta' => "/oferta/{$this->note->notable_id}/edit",
            default => '/',
        };
    }
}
