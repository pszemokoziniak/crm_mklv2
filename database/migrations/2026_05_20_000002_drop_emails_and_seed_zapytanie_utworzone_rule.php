<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DropEmailsAndSeedZapytanieUtworzoneRule extends Migration
{
    public function up()
    {
        if (Schema::hasTable('emails')) {
            Schema::drop('emails');
        }
        if (Schema::hasTable('emails_type')) {
            Schema::drop('emails_type');
        }

        $exists = DB::table('reminder_rules')
            ->where('event', 'zapytanie_utworzone')
            ->whereNull('deleted_at')
            ->exists();

        if (!$exists) {
            $now = now();
            DB::table('reminder_rules')->insert([
                'name' => 'Nowe zapytanie',
                'event' => 'zapytanie_utworzone',
                'days_before' => 0,
                'recipients' => json_encode(['opracowuje', 'role:super-admin', 'role:administrator']),
                'channels' => json_encode(['mail', 'database', 'webpush']),
                'subject' => 'Nowe zapytanie: {{projekt_nazwa}}',
                'body' => $this->body(),
                'active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down()
    {
        DB::table('reminder_rules')
            ->where('event', 'zapytanie_utworzone')
            ->where('name', 'Nowe zapytanie')
            ->delete();

        if (!Schema::hasTable('emails_type')) {
            Schema::create('emails_type', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('emails')) {
            Schema::create('emails', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_id');
                $table->unsignedInteger('type_id');
                $table->timestamps();
            });
        }
    }

    private function body(): string
    {
        return <<<HTML
<p>Cześć!</p>
<p>W systemie pojawiło się nowe zapytanie:</p>
<p><strong>{{projekt_nazwa}}</strong></p>
<p>Klient: {{client_nazwa}}</p>
<p>Zajrzyj do CRM, żeby zobaczyć szczegóły.</p>
HTML;
    }
}
