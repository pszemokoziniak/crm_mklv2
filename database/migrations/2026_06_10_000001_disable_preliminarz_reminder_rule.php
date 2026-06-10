<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class DisablePreliminarzReminderRule extends Migration
{
    public function up()
    {
        // Wyłaczamy regule "Nowe zapytanie (PRELIMINARZ)" bo email teraz idzie
        // bezpośrednio przez ZapytaniaMail z bogatym szablonem
        // (wracamy do mechanizmu z przed commitu b022545).
        DB::table('reminder_rules')
            ->where('event', 'zapytanie_utworzone')
            ->where('event_filter', 'preliminarz_only')
            ->whereNull('deleted_at')
            ->update([
                'active' => 0,
                'updated_at' => now(),
            ]);
    }

    public function down()
    {
        DB::table('reminder_rules')
            ->where('event', 'zapytanie_utworzone')
            ->where('event_filter', 'preliminarz_only')
            ->whereNull('deleted_at')
            ->update([
                'active' => 1,
                'updated_at' => now(),
            ]);
    }
}
