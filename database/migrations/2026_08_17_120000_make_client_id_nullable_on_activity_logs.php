<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MakeClientIdNullableOnActivityLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Log aktywności rekordu bez klienta (np. Przyszły Projekt bez GW) musi
        // dopuszczać pusty client_id. Klucz obcy pozostaje — dopuszcza NULL.
        DB::statement('ALTER TABLE activity_logs MODIFY client_id BIGINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE activity_logs MODIFY client_id BIGINT UNSIGNED NOT NULL');
    }
}
