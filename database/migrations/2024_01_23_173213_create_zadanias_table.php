<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZadaniasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zadanias', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('responsible_person_id')->unsigned()->index()->nullable(false);
            $table->text('subject');
            $table->text('description');
            $table->date('deadline');
            $table->bigInteger('user_id')->unsigned()->index()->nullable(false);
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
        Schema::dropIfExists('zadanias');
    }
}
