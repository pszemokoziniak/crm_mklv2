<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MakeClientIdNullableOnFutureProjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // GW (Generalny wykonawca) nie jest juz polem wymaganym.
        DB::statement('ALTER TABLE future_projects MODIFY client_id BIGINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE future_projects MODIFY client_id BIGINT UNSIGNED NOT NULL');
    }
}
