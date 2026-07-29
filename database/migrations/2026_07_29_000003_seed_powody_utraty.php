<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SeedPowodyUtraty extends Migration
{
    private const POWODY = [
        'Cena za wysoka',
        'Termin realizacji nie pasował',
        'Wygrała konkurencja',
        'Budżet klienta wstrzymany',
        'Brak kontaktu zwrotnego',
        'Zły zakres / specyfikacja',
        'Warunki płatności',
        'Inne',
    ];

    public function up()
    {
        $now = now();

        foreach (self::POWODY as $name) {
            $exists = DB::table('powody_utraty')->where('name', $name)->exists();
            if ($exists) {
                continue;
            }

            DB::table('powody_utraty')->insert([
                'name' => $name,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down()
    {
        DB::table('powody_utraty')->whereIn('name', self::POWODY)->delete();
    }
}
