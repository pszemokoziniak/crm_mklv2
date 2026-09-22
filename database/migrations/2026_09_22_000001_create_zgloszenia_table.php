<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZgloszeniaTable extends Migration
{
    public function up()
    {
        Schema::create('zgloszenia', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('url', 2048)->nullable();
            $table->string('status', 20)->default('do_zrobienia')->index();
            $table->string('priority', 20)->default('normalny');
            $table->unsignedBigInteger('reporter_id')->index();
            $table->unsignedBigInteger('assignee_id')->nullable()->index();
            $table->date('deadline')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('reporter_id')->references('id')->on('users');
            $table->foreign('assignee_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('zgloszenia');
    }
}
