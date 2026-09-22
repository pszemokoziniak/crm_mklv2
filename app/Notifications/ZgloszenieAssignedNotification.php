<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use App\Models\Zgloszenie;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Użytkownik został przypisany do zgłoszenia.
 */
class ZgloszenieAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private Zgloszenie $zgloszenie,
        private User $author,
    ) {
    }

    /**
     * @return string[]
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'assignment',
            'zgloszenie_id' => $this->zgloszenie->id,
            'author' => $this->author->first_name.' '.$this->author->last_name,
            'subject' => $this->zgloszenie->title,
            'excerpt' => 'Przypisano Ci zgłoszenie',
            'url' => '/zgloszenia/'.$this->zgloszenie->id,
        ];
    }
}
