<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdatePreliminarzReminderRuleRecipients extends Migration
{
    public function up()
    {
        DB::table('reminder_rules')
            ->where('event', 'zapytanie_utworzone')
            ->where('event_filter', 'preliminarz_only')
            ->whereNull('deleted_at')
            ->update([
                'recipients' => json_encode(['flag:preliminarz_email']),
                'active' => 1,
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
                'recipients' => json_encode(['opracowuje', 'role:super-admin', 'role:administrator']),
                'active' => 0,
                'updated_at' => now(),
            ]);
    }
}
