<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SeedZadaniaUtworzoneReminderRule extends Migration
{
    public function up()
    {
        $exists = DB::table('reminder_rules')
            ->where('event', 'zadania_utworzone')
            ->whereNull('deleted_at')
            ->exists();

        if ($exists) {
            return;
        }

        $now = now();

        DB::table('reminder_rules')->insert([
            'name' => 'Nowe zadanie',
            'event' => 'zadania_utworzone',
            'days_before' => 0,
            'recipients' => json_encode(['osoba_odpowiedzialna']),
            'channels' => json_encode(['mail', 'database', 'webpush']),
            'subject' => 'Nowe zadanie: {{projekt_nazwa}}',
            'body' => $this->body(),
            'active' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function down()
    {
        DB::table('reminder_rules')
            ->where('event', 'zadania_utworzone')
            ->where('name', 'Nowe zadanie')
            ->delete();
    }

    private function body(): string
    {
        return <<<HTML
<p>Cześć!</p>
<p>Zostało utworzone dla Ciebie nowe zadanie:</p>
<p><strong>{{projekt_nazwa}}</strong></p>
<p>Klient: {{client_nazwa}}</p>
<p>Termin: <strong>{{data}}</strong></p>
<p>Zajrzyj do CRM, żeby zobaczyć szczegóły.</p>
HTML;
    }
}
