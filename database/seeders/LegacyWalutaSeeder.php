<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Waluta;

class LegacyWalutaSeeder extends Seeder
{
    public function run()
    {
        $oldWaluty = DB::connection('old_crm')->table('mkl_waluty')->get();

        foreach ($oldWaluty as $oldWaluta) {
            Waluta::updateOrCreate(
                ['id' => $oldWaluta->id],
                [
                    'name' => strtoupper(trim($oldWaluta->waluta)),
                    'user_id' => 1,
                ]
            );
        }
    }
}
