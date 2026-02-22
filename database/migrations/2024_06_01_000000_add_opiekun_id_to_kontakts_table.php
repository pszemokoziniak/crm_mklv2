<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOpiekunIdToKontaktsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kontakts', function (Blueprint $table) {
            $table->bigInteger('opiekun_id')->unsigned()->index()->nullable()->after('user_id');
            $table->foreign('opiekun_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kontakts', function (Blueprint $table) {
            $table->dropForeign(['opiekun_id']);
            $table->dropColumn('opiekun_id');
        });
    }
}
