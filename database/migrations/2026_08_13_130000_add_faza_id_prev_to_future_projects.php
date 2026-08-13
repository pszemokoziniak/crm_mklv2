<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFazaIdPrevToFutureProjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('future_projects', function (Blueprint $table) {
            // Faza sprzed automatycznej archiwizacji (Zakończony) — do przywrócenia.
            $table->bigInteger('faza_id_prev')->unsigned()->nullable()->after('faza_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('future_projects', function (Blueprint $table) {
            $table->dropColumn('faza_id_prev');
        });
    }
}
