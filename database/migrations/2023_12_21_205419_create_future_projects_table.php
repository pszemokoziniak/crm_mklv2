<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFutureProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('future_projects', function (Blueprint $table) {
            $table->id();
            $table->text('nazwa')->nullable();
            $table->string('miasto',50)->nullable();
            $table->bigInteger('kraj_id')->unsigned()->index()->nullable(false);
            $table->bigInteger('objekt_id')->unsigned()->index()->nullable(false);
            $table->bigInteger('client_id')->unsigned()->index()->nullable(false);
            $table->text('opis')->nullable();
            $table->date('start')->nullable();
            $table->date('end')->nullable();
            $table->bigInteger('faza_id')->unsigned()->index()->nullable(false);
            $table->text('inwestor')->nullable(false);
            $table->text('dane_kontaktowe')->nullable();
            $table->date('data_kontakt')->nullable();
            $table->bigInteger('user_id')->unsigned()->index()->nullable(false);
            $table->boolean('arch')->nullable();
            $table->date('arch_time')->nullable();
            $table->bigInteger('arch_user')->nullable();

            $table->foreign('kraj_id')->references('id')->on('krajs');
            $table->foreign('objekt_id')->references('id')->on('objekts');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('faza_id')->references('id')->on('fazas');
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
        Schema::dropIfExists('future_projects');
    }
}
