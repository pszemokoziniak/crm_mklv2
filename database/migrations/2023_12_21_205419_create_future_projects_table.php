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
            $table->uuid('id')->primary();
            $table->text('nazwa')->nullable();
            $table->string('miasto',50)->nullable();
            $table->uuid('kraj_id')->nullable(false);
            $table->uuid('obiekt_id')->nullable(false);
            $table->uuid('client_id')->nullable(false);
            $table->text('opis')->nullable();
            $table->date('start')->nullable();
            $table->date('end')->nullable();
            $table->uuid('faza_id')->nullable(false);
            $table->text('inwestor')->nullable(false);
            $table->text('dane_kontaktowe')->nullable();
            $table->date('data_kontakt')->nullable();
            $table->uuid('user_id')->nullable(false);
            $table->boolean('arch')->nullable();
            $table->date('arch_time')->nullable();
            $table->integer('arch_user')->nullable();

            $table->foreign('kraj_id')->references('id')->on('krajs');
            $table->foreign('obiekt_id')->references('id')->on('obiekts');
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
