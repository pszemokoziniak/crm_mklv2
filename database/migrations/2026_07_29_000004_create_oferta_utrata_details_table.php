<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfertaUtrataDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('oferta_utrata_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('oferta_id')->unique();
            $table->unsignedBigInteger('powod_utraty_id');
            $table->unsignedBigInteger('powod_utraty_dodatkowy_id')->nullable();
            $table->string('etap_utraty', 50)->nullable();
            $table->string('konkurent', 200)->nullable();
            $table->decimal('cena_konkurenta', 12, 2)->nullable();
            $table->unsignedBigInteger('waluta_id')->nullable();
            $table->boolean('szansa_na_renegocjacje')->default(false);
            $table->text('notatka')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('oferta_id')->references('id')->on('ofertas')->onDelete('cascade');
            $table->foreign('powod_utraty_id')->references('id')->on('powody_utraty');
            $table->foreign('powod_utraty_dodatkowy_id')->references('id')->on('powody_utraty');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('oferta_utrata_details');
    }
}
