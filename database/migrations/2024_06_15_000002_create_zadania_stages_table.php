<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZadaniaStagesTable extends Migration
{
    public function up()
    {
        Schema::create('zadania_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zadania_id')->constrained('zadanias')->onDelete('cascade');
            $table->foreignId('milestone_id')->nullable()->constrained('zadania_milestones')->onDelete('cascade');
            $table->string('name');
            $table->integer('order')->default(0);
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('zadania_stages');
    }
}
