<?php

namespace Database\Seeders;

use App\Models\Branza;
use App\Models\Kraj;
use App\Models\Zakres;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('zakres')->insert([
            'name' => 'Zakres1',
        ]);
        DB::table('zakres')->insert([
            'name' => 'Zakres2',
        ]);

        DB::table('branzas')->insert([
            'name' => 'Branza1',
        ]);
        DB::table('branzas')->insert([
            'name' => 'Branza2',
        ]);

        DB::table('oferta_statuses')->insert([
            'name' => 'Przegrana',
        ]);

        DB::table('oferta_statuses')->insert([
            'name' => 'Wygrana',
        ]);

        DB::table('oferta_statuses')->insert([
            'name' => 'Rezygnacja',
        ]);

        DB::table('oferta_statuses')->insert([
            'name' => 'Toczy się',
        ]);

        DB::table('krajs')->insert([
            'name' => 'Polska',
            'waluta' => 'PLN',
        ]);

        DB::table('krajs')->insert([
            'name' => 'Monako',
            'waluta' => 'EUR',
        ]);
        DB::table('fazas')->insert([
            'name' => 'Faza1',
        ]);
        DB::table('fazas')->insert([
            'name' => 'Faza2',
        ]);
        DB::table('objekts')->insert([
            'name' => 'Objekt1',
        ]);
        DB::table('uprawnienias')->insert([
            'name' => 'Administrator',
        ]);
        DB::table('uprawnienias')->insert([
            'name' => 'Kierownictwo',
        ]);
        DB::table('uprawnienias')->insert([
            'name' => 'Techniczny',
        ]);
        DB::table('uprawnienias')->insert([
            'name' => 'Export',
        ]);
        DB::table('walutas')->insert([
            'name' => 'PLN',
            'user_id' => 1,
        ]);
        DB::table('walutas')->insert([
            'name' => 'EUR',
            'user_id' => 1,
        ]);
        DB::table('emails_type')->insert([
            'name' => 'Biuro',
        ]);
        DB::table('emails_type')->insert([
            'name' => 'Kierownictwo',
        ]);
    }
}
