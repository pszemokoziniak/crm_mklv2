<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClosureFieldsToZadaniasTable extends Migration
{
    public function up()
    {
        Schema::table('zadanias', function (Blueprint $table) {
            $table->unsignedBigInteger('closed_by')->nullable()->after('status');
            $table->timestamp('closed_at')->nullable()->after('closed_by');
            $table->foreign('closed_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('zadanias', function (Blueprint $table) {
            $table->dropForeign(['closed_by']);
            $table->dropColumn(['closed_by', 'closed_at']);
        });
    }
}
