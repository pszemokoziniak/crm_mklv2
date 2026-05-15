<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReminderRulesTable extends Migration
{
    public function up()
    {
        Schema::create('reminder_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('event', 64);
            $table->smallInteger('days_before')->default(0);
            $table->json('recipients');
            $table->string('subject', 200);
            $table->text('body');
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['active', 'event']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('reminder_rules');
    }
}
