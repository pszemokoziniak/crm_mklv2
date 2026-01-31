<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKrajsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('krajs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('waluta_id')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('waluta_id')->references('id')->on('walutas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('krajs');
    }
}
