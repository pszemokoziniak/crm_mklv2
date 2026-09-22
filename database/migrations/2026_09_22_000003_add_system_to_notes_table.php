<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSystemToNotesTable extends Migration
{
    /**
     * Wpisy systemowe (np. "Zmiana statusu: X → Y") w wątku zgłoszenia.
     * Notatki Zapytań/Ofert mają system=false i działają jak dotychczas.
     */
    public function up()
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->boolean('system')->default(false)->after('body');
        });
    }

    public function down()
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->dropColumn('system');
        });
    }
}
