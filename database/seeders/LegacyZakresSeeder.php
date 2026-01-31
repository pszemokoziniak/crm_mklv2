<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Zakres;

class LegacyZakresSeeder extends Seeder
{
    public function run()
    {
        $oldZakresy = DB::connection('old_crm')->table('mkl_zakres')->get();

        foreach ($oldZakresy as $oldZakres) {
            Zakres::updateOrCreate(
                ['id' => $oldZakres->id],
                [
                    'name' => $oldZakres->zakres,
                ]
            );
        }
    }
}
