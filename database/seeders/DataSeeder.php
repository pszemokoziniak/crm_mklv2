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
            'id' => Str::uuid(),
            'name' => 'Zakres1',
        ]);
        DB::table('zakres')->insert([
            'id' => Str::uuid(),
            'name' => 'Zakres2',
        ]);

        DB::table('branzas')->insert([
            'id' => Str::uuid(),
            'name' => 'Branza1',
        ]);
        DB::table('branzas')->insert([
            'id' => Str::uuid(),
            'name' => 'Branza2',
        ]);

        DB::table('oferta_statuses')->insert([
            'id' => Str::uuid(),
            'name' => 'Przegrana',
        ]);

        DB::table('oferta_statuses')->insert([
            'id' => Str::uuid(),
            'name' => 'Wygrana',
        ]);

        DB::table('oferta_statuses')->insert([
            'id' => Str::uuid(),
            'name' => 'Rezygnacja',
        ]);

        DB::table('oferta_statuses')->insert([
            'id' => Str::uuid(),
            'name' => 'Toczy się',
        ]);

        DB::table('krajs')->insert([
            'id' => Str::uuid(),
            'name' => 'Polska',
            'waluta' => 'PLN',
        ]);

        DB::table('krajs')->insert([
            'id' => Str::uuid(),
            'name' => 'Monako',
            'waluta' => 'EUR',
        ]);
        DB::table('fazas')->insert([
            'id' => Str::uuid(),
            'name' => 'Faza1',
        ]);
        DB::table('fazas')->insert([
            'id' => Str::uuid(),
            'name' => 'Faza2',
        ]);
        DB::table('objekts')->insert([
            'id' => Str::uuid(),
            'name' => 'Objekt1',
        ]);
        DB::table('objekts')->insert([
            'id' => Str::uuid(),
            'name' => 'Objekt2',
        ]);
    }
}
