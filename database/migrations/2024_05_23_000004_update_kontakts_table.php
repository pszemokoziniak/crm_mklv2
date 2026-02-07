<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateKontaktsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Usunięcie klucza obcego, aby móc zmienić kolumnę
        Schema::table('kontakts', function (Blueprint $table) {
            $table->dropForeign(['kontakt_person_id']);
        });

        // 2. Zmiany za pomocą Raw SQL (obejście braku Doctrine DBAL)
        // Zmiana nazwy i typu kontakt_person_id -> kontakt_id oraz ustawienie jako nullable
        DB::statement('ALTER TABLE kontakts CHANGE kontakt_person_id kontakt_id BIGINT UNSIGNED NULL');

        // Zmiana typu call_time z DATE na TIME
        DB::statement('ALTER TABLE kontakts MODIFY call_time TIME NOT NULL');

        // Dodanie call_date po client_id
        DB::statement('ALTER TABLE kontakts ADD call_date DATE AFTER client_id');

        // Ustawienie zapytania_id jako nullable
        DB::statement('ALTER TABLE kontakts MODIFY zapytania_id BIGINT UNSIGNED NULL');

        // 3. Przywrócenie klucza obcego z nową nazwą kolumny
        Schema::table('kontakts', function (Blueprint $table) {
            $table->foreign('kontakt_id')->references('id')->on('kontakt_persons');
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
            $table->dropForeign(['kontakt_id']);
        });

        DB::statement('ALTER TABLE kontakts CHANGE kontakt_id kontakt_person_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE kontakts MODIFY call_time DATE NOT NULL');
        DB::statement('ALTER TABLE kontakts DROP COLUMN call_date');
        DB::statement('ALTER TABLE kontakts MODIFY zapytania_id BIGINT UNSIGNED NOT NULL');

        Schema::table('kontakts', function (Blueprint $table) {
            $table->foreign('kontakt_person_id')->references('id')->on('kontakt_persons');
        });
    }
}
