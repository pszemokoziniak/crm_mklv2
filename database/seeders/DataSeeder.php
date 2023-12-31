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
            'name' => 'Status1',
        ]);

        DB::table('oferta_statuses')->insert([
            'id' => Str::uuid(),
            'name' => 'Status2',
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
    }
}
