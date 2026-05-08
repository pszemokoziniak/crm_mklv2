<?php

namespace App\Console\Commands;

use App\Models\Oferta;
use App\Models\User;
use App\Notifications\OfertaContactReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendOfertaContactReminders extends Command
{
    protected $signature = 'ofertas:contact-reminders';
    protected $description = 'Wysyła przypomnienia o zbliżających się terminach kontaktu z ofertami';

    public function handle()
    {
        $ofertas = Oferta::with(['user', 'zapytania', 'client', 'ofertastatus'])
            ->whereHas('ofertastatus', function ($q) {
                $q->where('name', 'TOCZY');
            })
            ->whereNotNull('data_kontakt')
            ->whereBetween('data_kontakt', [Carbon::today(), Carbon::today()->addDays(10)])
            ->get();

        $admins = User::role(['super-admin', 'administrator'])->get();

        $sent = 0;

        foreach ($ofertas as $oferta) {
            $daysUntil = (int) Carbon::today()->diffInDays($oferta->data_kontakt, false);
            $notification = new OfertaContactReminder($oferta, $daysUntil);

            $recipients = $admins->keyBy('id');

            if ($oferta->user && !$recipients->has($oferta->user_id)) {
                $recipients->put($oferta->user_id, $oferta->user);
            }

            Notification::send($recipients->values(), $notification);
            $sent++;
        }

        $this->info("Wysłano przypomnienia dla {$sent} ofert.");

        return 0;
    }
}
