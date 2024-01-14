<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKontaktsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kontakts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('client_id')->nullable(false);
            $table->date('call_time');
            $table->text('subject');
            $table->text('description');
            $table->uuid('user_id')->nullable(false);
            $table->uuid('kontakt_person_id')->nullable(false);
            $table->uuid('zapytania_id')->nullable();
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('zapytania_id')->references('id')->on('zapytanias');
            $table->foreign('kontakt_person_id')->references('id')->on('kontakt_persons');
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('kontakts');
    }
}
