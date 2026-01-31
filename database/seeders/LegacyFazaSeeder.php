<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Faza;

class LegacyFazaSeeder extends Seeder
{
    public function run()
    {
        $oldFazy = DB::connection('old_crm')->table('mkl_fazaProjekt')->get();

        foreach ($oldFazy as $oldFaza) {
            Faza::updateOrCreate(
                ['id' => $oldFaza->id],
                [
                    'name' => $oldFaza->faza,
                ]
            );
        }
    }
}
