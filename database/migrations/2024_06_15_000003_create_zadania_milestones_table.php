<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZadaniaMilestonesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('zadania_milestones')) {
            Schema::create('zadania_milestones', function (Blueprint $table) {
                $table->id();
                $table->foreignId('zadania_id')->constrained('zadanias')->onDelete('cascade');
                $table->string('name');
                $table->date('deadline');
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('zadania_milestones');
    }
}
