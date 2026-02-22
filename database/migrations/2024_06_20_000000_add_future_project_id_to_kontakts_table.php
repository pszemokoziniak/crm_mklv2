<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFutureProjectIdToKontaktsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Dodanie powiązania w tabeli kontakts
        Schema::table('kontakts', function (Blueprint $table) {
            $table->bigInteger('future_project_id')->unsigned()->index()->nullable()->after('oferta_id');
            $table->foreign('future_project_id')->references('id')->on('future_projects')->onDelete('cascade');
        });

        // Dodanie opiekuna do tabeli future_projects
        Schema::table('future_projects', function (Blueprint $table) {
            $table->bigInteger('opiekun_id')->unsigned()->index()->nullable()->after('user_id');
            $table->foreign('opiekun_id')->references('id')->on('users')->onDelete('set null');
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
            $table->dropForeign(['opiekun_id']);
            $table->dropColumn('opiekun_id');
        });

        Schema::table('kontakts', function (Blueprint $table) {
            $table->dropForeign(['future_project_id']);
            $table->dropColumn('future_project_id');
        });
    }
}
