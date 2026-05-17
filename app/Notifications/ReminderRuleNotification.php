<?php

namespace App\Notifications;

use App\Models\ReminderRule;
use App\Services\ReminderTemplateRenderer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class ReminderRuleNotification extends Notification
{
    use Queueable;

    protected ReminderRule $rule;
    protected $subjectModel;
    protected int $daysUntil;

    public function __construct(ReminderRule $rule, $subjectModel, int $daysUntil)
    {
        $this->rule = $rule;
        $this->subjectModel = $subjectModel;
        $this->daysUntil = $daysUntil;
    }

    public function via($notifiable)
    {
        $map = [
            ReminderRule::CHANNEL_MAIL => 'mail',
            ReminderRule::CHANNEL_DATABASE => 'database',
            ReminderRule::CHANNEL_WEBPUSH => WebPushChannel::class,
        ];

        $channels = [];
        foreach ($this->rule->channels as $c) {
            if (isset($map[$c])) {
                $channels[] = $map[$c];
            }
        }

        if (empty($notifiable->email ?? null)) {
            $channels = array_values(array_filter($channels, fn ($x) => $x !== 'mail'));
        }

        return $channels ?: ['database'];
    }

    public function toMail($notifiable)
    {
        $renderer = new ReminderTemplateRenderer();
        $subject = $renderer->render($this->rule->subject, $this->subjectModel, $this->daysUntil);
        $body = $renderer->render($this->rule->body, $this->subjectModel, $this->daysUntil);
        $placeholders = $renderer->placeholders($this->subjectModel, $this->daysUntil);

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.reminder', [
                'subject' => $subject,
                'body' => $body,
                'url' => $placeholders['{{url}}'] ?? '',
            ]);
    }

    public function toArray($notifiable)
    {
        $renderer = new ReminderTemplateRenderer();
        $placeholders = $renderer->placeholders($this->subjectModel, $this->daysUntil);
        $title = $renderer->render($this->rule->subject, $this->subjectModel, $this->daysUntil);

        return [
            'message' => $title,
            'url' => $placeholders['{{url}}'] ?? '',
            'rule_id' => $this->rule->id,
            'event' => $this->rule->event,
            'subject_type' => $this->subjectModel ? get_class($this->subjectModel) : null,
            'subject_id' => $this->subjectModel->id ?? null,
        ];
    }

    public function toWebPush($notifiable, $notification)
    {
        $renderer = new ReminderTemplateRenderer();
        $placeholders = $renderer->placeholders($this->subjectModel, $this->daysUntil);

        $title = $renderer->render($this->rule->subject, $this->subjectModel, $this->daysUntil);
        $projekt = $placeholders['{{projekt_nazwa}}'] ?? '';
        $url = $placeholders['{{url}}'] ?? '/';

        $body = $this->daysUntil === 0
            ? "Dzisiaj termin: \"{$projekt}\""
            : "Za {$this->daysUntil} dni termin: \"{$projekt}\"";

        $message = (new WebPushMessage)
            ->title($title)
            ->icon('/favicon.svg')
            ->body($body)
            ->data(['url' => $url]);

        if ($url) {
            $message->action('Przejdź', $url);
        }

        return $message;
    }
}
