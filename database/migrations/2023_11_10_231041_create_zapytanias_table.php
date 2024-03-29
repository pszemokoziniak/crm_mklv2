<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZapytaniasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zapytanias', function (Blueprint $table) {
            $table->id();
            $table->string('id_zapyt', 18);
            $table->bigInteger('user_otrzymal_id')->unsigned()->index()->nullable(false);
            $table->date('data_otrzymania')->nullable();
            $table->date('data_zlozenia')->nullable();
            $table->bigInteger('client_id')->unsigned()->nullable(false);
            $table->text('nazwa_projektu')->nullable();
            $table->string('preliminarz')->nullable();
            $table->string('miejscowosc', 100)->nullable();
            $table->bigInteger('kraj_id')->unsigned()->index()->nullable(false);
            $table->bigInteger('zakres_id')->unsigned()->index()->nullable(false);
            $table->bigInteger('user_opracowuje_id')->unsigned()->index()->nullable(false);

            $table->date('start')->nullable();
            $table->date('end')->nullable();
            $table->bigInteger('kwota')->nullable();
            $table->bigInteger('waluta_id')->nullable();
            $table->text('opis')->nullable();
            $table->string('miasto',50)->nullable();
            $table->bigInteger('user_id')->unsigned()->index()->nullable(false);
            $table->decimal('kurs', 8,4)->nullable();
            $table->decimal('kwotaPLN', 15,2)->nullable();
            $table->boolean('arch')->nullable();
            $table->date('arch_time')->nullable();
            $table->integer('arch_user')->nullable();
            $table->integer('wznowienie')->nullable();

            $table->foreign('user_otrzymal_id')->references('id')->on('users');
            $table->foreign('kraj_id')->references('id')->on('krajs');
            $table->foreign('zakres_id')->references('id')->on('zakres');
            $table->foreign('user_opracowuje_id')->references('id')->on('users');
            $table->foreign('user_id')->references('id')->on('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zapytanias');
    }
}
