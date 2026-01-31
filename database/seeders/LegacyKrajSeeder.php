<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Kraj;
use App\Models\Waluta;

class LegacyKrajSeeder extends Seeder
{
    public function run()
    {
        $oldKraje = DB::connection('old_crm')->table('mkl_kraje')->get();

        foreach ($oldKraje as $oldKraj) {
            // Sprawdzamy, czy waluta o tym ID istnieje w nowej bazie
            $walutaId = $oldKraj->idWal;
            if (!Waluta::find($walutaId)) {
                $walutaId = null; // Jeśli nie ma takiej waluty, ustawiamy null
            }

            Kraj::updateOrCreate(
                ['id' => $oldKraj->idWal],
                [
                    'name' => $oldKraj->kraj,
                    'waluta_id' => $walutaId,
                ]
            );
        }
    }
}
