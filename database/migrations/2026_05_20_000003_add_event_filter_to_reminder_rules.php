<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEventFilterToReminderRules extends Migration
{
    public function up()
    {
        Schema::table('reminder_rules', function (Blueprint $table) {
            $table->string('event_filter', 64)->nullable()->after('event');
        });
    }

    public function down()
    {
        Schema::table('reminder_rules', function (Blueprint $table) {
            $table->dropColumn('event_filter');
        });
    }
}
