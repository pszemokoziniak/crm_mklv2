<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MakeKontaktPersonsOptionalColumnsNullable extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE `kontakt_persons` MODIFY `position` VARCHAR(255) NULL");
        DB::statement("ALTER TABLE `kontakt_persons` MODIFY `phone1` VARCHAR(255) NULL");
        DB::statement("ALTER TABLE `kontakt_persons` MODIFY `phone2` VARCHAR(255) NULL");
        DB::statement("ALTER TABLE `kontakt_persons` MODIFY `email` VARCHAR(255) NULL");
        DB::statement("ALTER TABLE `kontakt_persons` MODIFY `description` TEXT NULL");
    }

    public function down()
    {
        DB::statement("ALTER TABLE `kontakt_persons` MODIFY `position` VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE `kontakt_persons` MODIFY `phone1` VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE `kontakt_persons` MODIFY `phone2` VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE `kontakt_persons` MODIFY `email` VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE `kontakt_persons` MODIFY `description` TEXT NOT NULL");
    }
}
