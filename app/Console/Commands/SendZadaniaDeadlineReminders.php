<?php

namespace App\Console\Commands;

use App\Models\Zadania;
use App\Models\User;
use App\Notifications\ZadaniaDeadlineReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendZadaniaDeadlineReminders extends Command
{
    protected $signature = 'zadania:deadline-reminders';
    protected $description = 'Wysyła przypomnienia o zbliżających się terminach zadań';

    public function handle()
    {
        $zadania = Zadania::with(['responsiblePerson'])
            ->where('status', Zadania::STATUS_AKTYWNE)
            ->whereNotNull('deadline')
            ->whereBetween('deadline', [Carbon::today(), Carbon::today()->addDays(10)])
            ->get();

        $admins = User::role(['super-admin', 'administrator'])->get();

        $sent = 0;

        foreach ($zadania as $task) {
            $daysUntil = (int) Carbon::today()->diffInDays($task->deadline, false);
            $notification = new ZadaniaDeadlineReminder($task, $daysUntil);

            $recipients = $admins->keyBy('id');

            if ($task->responsiblePerson && !$recipients->has($task->responsible_person_id)) {
                $recipients->put($task->responsible_person_id, $task->responsiblePerson);
            }

            Notification::send($recipients->values(), $notification);
            $sent++;
        }

        $this->info("Wysłano przypomnienia dla {$sent} zadań.");

        return 0;
    }
}
