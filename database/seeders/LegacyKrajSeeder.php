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

        $walutaMapping = [
            'EUR' => 1,
            'GBP' => 2,
            'PLN' => 3,
            'USD' => 4,
        ];

        foreach ($oldKraje as $oldKraj) {
            // W starej tabeli pole z walutą (string) to idWal
            $oldWalutaName = strtoupper(trim($oldKraj->idWal ?? ''));

            // Mapujemy na ID nowej waluty (int)
            $walutaId = $walutaMapping[$oldWalutaName] ?? null;

            // Jeśli nie znaleziono w mapowaniu, spróbujmy wyszukać w bazie po nazwie
            if (!$walutaId && $oldWalutaName) {
                $waluta = Waluta::where('name', $oldWalutaName)->first();
                $walutaId = $waluta ? $waluta->id : null;
            }

            // Skoro $oldKraj->id nie istnieje, używamy nazwy kraju jako klucza
            Kraj::updateOrCreate(
                ['name' => $oldKraj->kraj],
                [
                    'waluta_id' => $walutaId,
                ]
            );
        }
    }
}
