<?php

namespace App\Console\Commands;

use App\Models\FutureProject;
use App\Models\Oferta;
use App\Models\ReminderRule;
use App\Models\Zapytania;
use App\Models\ZapytaniaWznowienie;
use App\Notifications\ReminderRuleNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class DispatchReminderRules extends Command
{
    protected $signature = 'reminders:dispatch {--dry-run : Wypisz tylko, co zostałoby wysłane, bez wysyłania}';
    protected $description = 'Wysyła przypomnienia mailowe zgodnie z regułami w reminder_rules';

    public function handle()
    {
        $dryRun = (bool) $this->option('dry-run');
        $rules = ReminderRule::where('active', true)->get();

        if ($rules->isEmpty()) {
            $this->info('Brak aktywnych reguł.');
            return 0;
        }

        $totalSent = 0;

        foreach ($rules as $rule) {
            $records = $this->recordsForRule($rule);
            $daysUntil = (int) $rule->days_before;

            foreach ($records as $record) {
                $recipients = $rule->resolveRecipients($record);

                if ($recipients->isEmpty()) {
                    $this->warn("[{$rule->name}] rekord #{$record->id}: brak odbiorców z emailem");
                    continue;
                }

                if ($dryRun) {
                    $emails = $recipients->pluck('email')->implode(', ');
                    $this->line("[DRY] [{$rule->name}] -> {$emails} | rekord #{$record->id}");
                    $totalSent++;
                    continue;
                }

                Notification::send($recipients, new ReminderRuleNotification($rule, $record, $daysUntil));
                $totalSent++;
            }
        }

        $this->info(($dryRun ? '[DRY] ' : '')."Wysłano przypomnień: {$totalSent}");
        return 0;
    }

    private function recordsForRule(ReminderRule $rule)
    {
        $target = Carbon::today()->addDays((int) $rule->days_before)->toDateString();

        switch ($rule->event) {
            case ReminderRule::EVENT_OFERTA_KONTAKT:
                return Oferta::with(['user', 'client', 'zapytania.client'])
                    ->whereNotNull('data_kontakt')
                    ->whereDate('data_kontakt', $target)
                    ->get();

            case ReminderRule::EVENT_ZAPYTANIE_TERMIN_ZLOZENIA:
                return Zapytania::with(['user', 'opracowuje', 'client', 'oferty'])
                    ->whereNotNull('data_zlozenia')
                    ->whereDate('data_zlozenia', $target)
                    ->whereDoesntHave('oferty')
                    ->get();

            case ReminderRule::EVENT_WZNOWIENIE_TERMIN:
                return ZapytaniaWznowienie::with(['user', 'zapytania.client'])
                    ->whereNotNull('data_zlozenia')
                    ->whereDate('data_zlozenia', $target)
                    ->get();

            case ReminderRule::EVENT_FUTURE_PROJECT_START:
                return FutureProject::with(['user', 'client'])
                    ->whereNotNull('data_start')
                    ->whereDate('data_start', $target)
                    ->get();
        }

        return collect();
    }
}
