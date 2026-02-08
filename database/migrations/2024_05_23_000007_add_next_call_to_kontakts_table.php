<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNextCallToKontaktsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kontakts', function (Blueprint $table) {
            $table->date('next_call_date')->nullable()->after('call_time');
            $table->time('next_call_time')->nullable()->after('next_call_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kontakts', function (Blueprint $table) {
            $table->dropColumn(['next_call_date', 'next_call_time']);
        });
    }
}
