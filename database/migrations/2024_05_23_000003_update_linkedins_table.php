<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLinkedinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('linkedins', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->unsignedBigInteger('client_id')->nullable()->after('id');
            $table->unsignedBigInteger('user_id')->nullable()->after('client_id');

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('linkedins', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn(['client_id', 'user_id']);
            $table->string('name')->after('id');
        });
    }
}
