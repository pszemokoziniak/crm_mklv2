<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action')->nullable(false);
            $table->bigInteger('link_id')->unsigned()->index()->nullable(false);
            $table->bigInteger('client_id')->unsigned()->index()->nullable(false);
            $table->string('link_action')->nullable(false);
            $table->string('changes')->nullable(false);
            $table->bigInteger('user_id')->unsigned()->index()->nullable(false);

            $table->foreign('client_id')->references('id')->on('clients');
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
        Schema::dropIfExists('activity_logs');
    }
}
