<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCanEditAllToUsers extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Flaga per-user: moze przegladac i edytowac Zapytania/Oferty innych userow
            // (jak Kierownictwo), bez nadawania roli Administrator.
            $table->boolean('can_edit_all')->default(false)->after('preliminarz_email');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('can_edit_all');
        });
    }
}
