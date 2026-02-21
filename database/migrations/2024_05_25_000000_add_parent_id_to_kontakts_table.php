<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('kontakts', function (Blueprint $column) {
            $column->foreignId('parent_id')->nullable()->after('id')->constrained('kontakts')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('kontakts', function (Blueprint $column) {
            $column->dropForeign(['parent_id']);
            $column->dropColumn('parent_id');
        });
    }
};
