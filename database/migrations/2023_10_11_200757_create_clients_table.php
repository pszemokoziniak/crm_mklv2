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
            $table->uuid('id')->primary();
            $table->string('nazwa', 200);
            $table->string('ulica', 200)->nullable();
            $table->string('miasto', 200)->nullable();
            $table->string('www', 200)->nullable();
            $table->string('linkedIn', 200)->nullable();
            $table->text('message')->nullable();
            $table->uuid('user_id')->nullable(false);
            $table->uuid('kraj_id')->nullable(false);
            $table->uuid('branza_id')->nullable(false);
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
        Schema::dropIfExists('clients');
    }
}
