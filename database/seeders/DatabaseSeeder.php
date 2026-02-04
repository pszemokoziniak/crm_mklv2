<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Account;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();

//        DB::table('users')->truncate();
        DB::table('accounts')->truncate();
        DB::table('branzas')->truncate();
        DB::table('krajs')->truncate();
        DB::table('zakres')->truncate();
        DB::table('oferta_statuses')->truncate();
        DB::table('walutas')->truncate();
        DB::table('kursies')->truncate();
        DB::table('fazas')->truncate();
        DB::table('objekts')->truncate();
        DB::table('clients')->truncate();
        DB::table('zapytanias')->truncate();
        DB::table('ofertas')->truncate();

        Schema::enableForeignKeyConstraints();

        Account::create(['id' => 1, 'name' => 'MKL CRM']);

        $this->call([
//            LegacyUserSeeder::class,
            LegacyBranzaSeeder::class,
            LegacyWalutaSeeder::class,
            LegacyKrajSeeder::class,
            LegacyZakresSeeder::class,
            LegacyOfertaStatusSeeder::class,
            LegacyKursySeeder::class,
            LegacyFazaSeeder::class,
            LegacyObjektSeeder::class,
            LegacyClientSeeder::class,
            LegacyZapytaniaSeeder::class,
            LegacyOfertaSeeder::class,
        ]);
    }
}
