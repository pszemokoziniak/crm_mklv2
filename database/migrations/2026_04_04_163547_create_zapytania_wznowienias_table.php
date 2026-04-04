<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZapytaniaWznowieniasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zapytania_wznowienias', function (Blueprint $table) {
            $table->id();
            $table->text('text');
            $table->unsignedBigInteger('id_zapytania')->index();
            $table->unsignedBigInteger('id_user')->nullable()->index();
            $table->timestamp('time')->useCurrent();

            $table->foreign('id_zapytania')->references('id')->on('zapytanias')->onDelete('cascade');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zapytania_wznowienias');
    }
}
