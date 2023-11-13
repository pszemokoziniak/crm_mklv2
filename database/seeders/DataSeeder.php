<?php

namespace Database\Seeders;

use App\Models\Branza;
use App\Models\Kraj;
use App\Models\Zakres;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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

        DB::table('krajs')->insert([
            'name' => 'Polska',
            'waluta' => 'PLN',
        ]);

        DB::table('krajs')->insert([
            'name' => 'Monako',
            'waluta' => 'EUR',
        ]);
    }
}
