<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClientIdToZadaniasTable extends Migration
{
    public function up()
    {
        Schema::table('zadanias', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->nullable()->after('user_id');
            $table->foreign('client_id')->references('id')->on('clients')->nullOnDelete();
            $table->index('client_id');
        });
    }

    public function down()
    {
        Schema::table('zadanias', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropIndex(['client_id']);
            $table->dropColumn('client_id');
        });
    }
}
