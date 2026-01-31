<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Branza;

class LegacyBranzaSeeder extends Seeder
{
    public function run()
    {
        $oldBranze = DB::connection('old_crm')->table('mkl_branza')->get();

        foreach ($oldBranze as $oldBranza) {
            Branza::updateOrCreate(
                ['id' => $oldBranza->idBr],
                [
                    'name' => $oldBranza->branza,
                ]
            );
        }
    }
}
