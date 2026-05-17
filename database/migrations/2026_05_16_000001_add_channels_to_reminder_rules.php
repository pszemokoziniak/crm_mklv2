<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddChannelsToReminderRules extends Migration
{
    public function up()
    {
        Schema::table('reminder_rules', function (Blueprint $table) {
            $table->json('channels')->nullable()->after('recipients');
        });

        DB::table('reminder_rules')->whereNull('channels')->update([
            'channels' => json_encode(['mail']),
        ]);
    }

    public function down()
    {
        Schema::table('reminder_rules', function (Blueprint $table) {
            $table->dropColumn('channels');
        });
    }
}
