<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Objekt;

class LegacyObjektSeeder extends Seeder
{
    public function run()
    {
        $oldObiekty = DB::connection('old_crm')->table('mkl_projectObjekt')->get();

        foreach ($oldObiekty as $oldObiekt) {
            Objekt::updateOrCreate(
                ['id' => $oldObiekt->id],
                [
                    'name' => $oldObiekt->nazwa_objektu,
                    'created_at' => $oldObiekt->input_date,
                ]
            );
        }
    }
}
