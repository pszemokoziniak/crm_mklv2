<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKwotaToFutureProjectsTable extends Migration
{
    public function up()
    {
        Schema::table('future_projects', function (Blueprint $table) {
            if (! Schema::hasColumn('future_projects', 'kwota')) {
                $table->decimal('kwota', 15, 2)->nullable()->after('dane_kontaktowe');
            }
        });
    }

    public function down()
    {
        Schema::table('future_projects', function (Blueprint $table) {
            if (Schema::hasColumn('future_projects', 'kwota')) {
                $table->dropColumn('kwota');
            }
        });
    }
}
