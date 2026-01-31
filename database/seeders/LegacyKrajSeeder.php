<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Kraj;

class LegacyKrajSeeder extends Seeder
{
    public function run()
    {
        $oldKraje = DB::connection('old_crm')->table('mkl_kraje')->get();

        foreach ($oldKraje as $oldKraj) {
            Kraj::updateOrCreate(
                ['id' => $oldKraj->idWal],
                [
                    'name' => $oldKraj->kraj,
                    'waluta' => $oldKraj->waluta,
                ]
            );
        }
    }
}
