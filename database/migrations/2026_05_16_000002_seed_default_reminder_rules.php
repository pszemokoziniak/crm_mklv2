<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SeedDefaultReminderRules extends Migration
{
    public function up()
    {
        $now = now();

        $defaults = [
            [
                'name' => 'Termin kontaktu z oferty',
                'event' => 'oferta_kontakt',
                'days_before' => 3,
                'recipients' => json_encode(['opracowuje', 'role:super-admin', 'role:administrator']),
                'channels' => json_encode(['mail', 'database', 'webpush']),
                'subject' => 'Przypomnienie o kontakcie: {{projekt_nazwa}}',
                'body' => $this->bodyOferta(),
                'active' => 1,
            ],
            [
                'name' => 'Termin zadania',
                'event' => 'zadania_deadline',
                'days_before' => 3,
                'recipients' => json_encode(['osoba_odpowiedzialna', 'role:super-admin', 'role:administrator']),
                'channels' => json_encode(['mail', 'database', 'webpush']),
                'subject' => 'Przypomnienie o zadaniu: {{projekt_nazwa}}',
                'body' => $this->bodyZadanie(),
                'active' => 1,
            ],
        ];

        foreach ($defaults as $row) {
            $exists = DB::table('reminder_rules')
                ->where('event', $row['event'])
                ->whereNull('deleted_at')
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('reminder_rules')->insert(array_merge($row, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }

    public function down()
    {
        DB::table('reminder_rules')
            ->whereIn('event', ['oferta_kontakt', 'zadania_deadline'])
            ->whereIn('name', ['Termin kontaktu z oferty', 'Termin zadania'])
            ->delete();
    }

    private function bodyOferta(): string
    {
        return <<<HTML
<p>Cześć!</p>
<p>Za <strong>{{days_until}}</strong> dni przypada termin kontaktu z klientem dla oferty:</p>
<p><strong>{{projekt_nazwa}}</strong> ({{client_nazwa}})</p>
<p>Data kontaktu: <strong>{{data}}</strong></p>
HTML;
    }

    private function bodyZadanie(): string
    {
        return <<<HTML
<p>Cześć!</p>
<p>Za <strong>{{days_until}}</strong> dni przypada termin zadania:</p>
<p><strong>{{projekt_nazwa}}</strong></p>
<p>Deadline: <strong>{{data}}</strong></p>
HTML;
    }
}
