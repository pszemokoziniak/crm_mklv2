<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToZadaniasTable extends Migration
{
    public function up()
    {
        Schema::table('zadanias', function (Blueprint $table) {
            $table->string('status')->default('aktywne')->after('user_id');
        });
    }

    public function down()
    {
        Schema::table('zadanias', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
