<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('nazwa', 200);
            $table->string('ulica', 200);
            $table->string('miasto', 200);
            $table->string('kraj', 200);
            $table->string('www', 200);
            $table->string('linkedIn', 200);
            $table->integer('branza')->index();
            $table->string('waluta', 3);
            $table->text('message');
            $table->string('dodal', 200);
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
        Schema::dropIfExists('clients');
    }
}
