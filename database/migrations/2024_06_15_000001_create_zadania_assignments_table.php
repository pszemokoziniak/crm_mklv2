<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZadaniaAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zadania_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zadania_id')->constrained('zadanias')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->comment('The assigned responsible person');
            $table->foreignId('assigned_by')->constrained('users')->comment('The user who made the assignment');
            $table->timestamp('assigned_at')->useCurrent();
            $table->text('note')->nullable()->comment('Optional note about why the assignment changed');
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
        Schema::dropIfExists('zadania_assignments');
    }
}
