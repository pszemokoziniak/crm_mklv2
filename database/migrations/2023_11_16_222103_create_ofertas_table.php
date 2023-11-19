<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfertasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ofertas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('typ')->nullable(false);
            $table->uuid('zapytania_id')->nullable(false);
            $table->uuid('client_id')->nullable(false);
            $table->date('data_wyslania')->nullable();
            $table->bigInteger('kwota')->nullable();
            $table->text('waluta')->nullable();
            $table->text('kurs')->nullable();
            $table->text('kwotaPLN')->nullable();
            $table->date('data_kontakt')->nullable();
            $table->uuid('oferta_status_id')->nullable(false);
            $table->text('opis')->nullable();
            $table->uuid('arch_user_id')->nullable();
            $table->text('arch_text')->nullable();
            $table->date('arch_time')->nullable();
            $table->uuid('user_id');

            $table->foreign('zapytania_id')->references('id')->on('zapytanias');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('oferta_status_id')->references('id')->on('oferta_statuses');
            $table->foreign('arch_user_id')->references('id')->on('users');
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
        Schema::dropIfExists('ofertas');
    }
}
