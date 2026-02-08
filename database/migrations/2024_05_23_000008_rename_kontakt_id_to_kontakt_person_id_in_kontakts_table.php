<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RenameKontaktIdToKontaktPersonIdInKontaktsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Obsługa zmiany nazwy kolumny lub jej utworzenia
        if (Schema::hasColumn('kontakts', 'kontakt_id')) {
            try {
                DB::statement('ALTER TABLE kontakts DROP FOREIGN KEY kontakts_kontakt_id_foreign');
            } catch (\Exception $e) {}

            DB::statement('ALTER TABLE kontakts CHANGE kontakt_id kontakt_person_id BIGINT UNSIGNED NULL');
        } elseif (!Schema::hasColumn('kontakts', 'kontakt_person_id')) {
            Schema::table('kontakts', function (Blueprint $table) {
                $table->bigInteger('kontakt_person_id')->unsigned()->nullable()->after('user_id');
            });
        }

        // 2. CZYSZCZENIE DANYCH: Ustawiamy NULL tam, gdzie osoba kontaktowa nie istnieje
        // To zapobiegnie błędowi Integrity constraint violation (1452)
        DB::statement("UPDATE kontakts SET kontakt_person_id = NULL WHERE kontakt_person_id NOT IN (SELECT id FROM kontakt_persons) OR kontakt_person_id = 0");

        try {
            DB::statement('ALTER TABLE kontakts DROP FOREIGN KEY kontakts_kontakt_person_id_foreign');
        } catch (\Exception $e) {}

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 3. Nałożenie klucza obcego
        Schema::table('kontakts', function (Blueprint $table) {
            $table->foreign('kontakt_person_id')
                ->references('id')
                ->on('kontakt_persons')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            DB::statement('ALTER TABLE kontakts DROP FOREIGN KEY kontakts_kontakt_person_id_foreign');
        } catch (\Exception $e) {}

        if (Schema::hasColumn('kontakts', 'kontakt_person_id')) {
            DB::statement('ALTER TABLE kontakts CHANGE kontakt_person_id kontakt_id BIGINT UNSIGNED NULL');
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
