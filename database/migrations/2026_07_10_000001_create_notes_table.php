<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotesTable extends Migration
{
    public function up()
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // autor notatki
            $table->string('notable_type'); // np. App\Models\Zapytania
            $table->unsignedBigInteger('notable_id');
            $table->text('body');
            $table->timestamps();

            $table->index(['notable_type', 'notable_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notes');
    }
}
