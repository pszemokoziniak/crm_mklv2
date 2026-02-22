<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMilestoneIdToZadaniaStagesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('zadania_stages', 'milestone_id')) {
            Schema::table('zadania_stages', function (Blueprint $table) {
                $table->foreignId('milestone_id')->nullable()->after('zadania_id')->constrained('zadania_milestones')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::table('zadania_stages', function (Blueprint $table) {
            $table->dropForeign(['milestone_id']);
            $table->dropColumn('milestone_id');
        });
    }
}
