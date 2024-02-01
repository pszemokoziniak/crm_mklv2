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
            $table->id();
            $table->text('typ')->nullable(false);
            $table->bigInteger('zapytania_id')->unsigned()->index()->nullable(false);
            $table->bigInteger('client_id')->unsigned()->index()->nullable(false);
            $table->date('data_wyslania')->nullable();
            $table->bigInteger('kwota')->nullable();
            $table->bigInteger('waluta_id')->index()->nullable(false);
            $table->decimal('kurs', 8, 4)->nullable();
            $table->float('kwotaPLN', 10,2)->nullable();
            $table->date('data_kontakt')->nullable();
            $table->bigInteger('oferta_status_id')->unsigned()->index()->nullable(false);
            $table->text('opis')->nullable();
            $table->bigInteger('arch_user_id')->unsigned()->index()->nullable();
            $table->text('arch_text')->nullable();
            $table->date('arch_time')->nullable();
            $table->bigInteger('user_id')->unsigned()->index()->nullable(false);

            $table->foreign('zapytania_id')->references('id')->on('zapytanias');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('oferta_status_id')->references('id')->on('oferta_statuses');
            $table->foreign('arch_user_id')->references('id')->on('users');
//            $table->foreign('waluta_id')->references('id')->on('walutas');
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
