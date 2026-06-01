<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SeedZapytaniePreliminarzReminderRule extends Migration
{
    public function up()
    {
        $exists = DB::table('reminder_rules')
            ->where('event', 'zapytanie_utworzone')
            ->where('event_filter', 'preliminarz_only')
            ->whereNull('deleted_at')
            ->exists();

        if ($exists) {
            return;
        }

        $now = now();

        DB::table('reminder_rules')->insert([
            'name' => 'Nowe zapytanie (PRELIMINARZ)',
            'event' => 'zapytanie_utworzone',
            'event_filter' => 'preliminarz_only',
            'days_before' => 0,
            'recipients' => json_encode(['opracowuje', 'role:super-admin', 'role:administrator']),
            'channels' => json_encode(['mail', 'database', 'webpush']),
            'subject' => 'Nowe zapytanie PRELIMINARZ: {{projekt_nazwa}}',
            'body' => $this->body(),
            'active' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function down()
    {
        DB::table('reminder_rules')
            ->where('event', 'zapytanie_utworzone')
            ->where('event_filter', 'preliminarz_only')
            ->where('name', 'Nowe zapytanie (PRELIMINARZ)')
            ->delete();
    }

    private function body(): string
    {
        return <<<HTML
<p>Cześć!</p>
<p>W systemie pojawiło się nowe zapytanie z opcją <strong>PRELIMINARZ</strong>:</p>
<p><strong>{{projekt_nazwa}}</strong></p>
<p>Klient: {{client_nazwa}}</p>
<p>Zajrzyj do CRM, żeby zobaczyć szczegóły.</p>
HTML;
    }
}
