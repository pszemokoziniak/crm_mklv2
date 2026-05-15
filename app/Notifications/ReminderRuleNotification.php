<?php

namespace App\Notifications;

use App\Models\ReminderRule;
use App\Services\ReminderTemplateRenderer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

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
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $renderer = new ReminderTemplateRenderer();
        $subject = $renderer->render($this->rule->subject, $this->subjectModel, $this->daysUntil);
        $body = $renderer->render($this->rule->body, $this->subjectModel, $this->daysUntil);

        $mail = (new MailMessage)->subject($subject);

        foreach (preg_split("/\r\n|\n|\r/", $body) as $line) {
            $mail->line($line);
        }

        $placeholders = $renderer->placeholders($this->subjectModel, $this->daysUntil);
        if (!empty($placeholders['{{url}}'])) {
            $mail->action('Przejdź do rekordu', $placeholders['{{url}}']);
        }

        return $mail->salutation('Pozdrawiamy, Zespół MKL CRM');
    }
}
