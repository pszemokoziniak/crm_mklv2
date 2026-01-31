<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Waluta;

class LegacyWalutaSeeder extends Seeder
{
    public function run()
    {
        // Pobieramy unikalne waluty ze starej tabeli krajów
        $oldWaluty = DB::connection('old_crm')->table('mkl_kraje')->select('waluta')->distinct()->get();

        foreach ($oldWaluty as $oldWaluta) {
            if (empty($oldWaluta->waluta)) continue;

            Waluta::updateOrCreate(
                ['name' => $oldWaluta->waluta],
                [
                    'user_id' => 1, // Domyślny admin
                ]
            );
        }
    }
}
