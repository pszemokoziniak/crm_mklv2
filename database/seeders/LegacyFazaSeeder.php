<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Faza;

class LegacyFazaSeeder extends Seeder
{
    public function run()
    {
        $oldFazy = DB::connection('old_crm')->table('fazas')->get();

        foreach ($oldFazy as $oldFaza) {
            Faza::updateOrCreate(
                ['id' => $oldFaza->id],
                [
                    'name' => $oldFaza->name,
                    'created_at' => $oldFaza->created_at,
                    'updated_at' => $oldFaza->updated_at,
                    'deleted_at' => $oldFaza->deleted_at,
                ]
            );
        }
    }
}
