<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Kursy;
use App\Models\Waluta;

class LegacyKursySeeder extends Seeder
{
    public function run()
    {
        $oldKursy = DB::connection('old_crm')->table('mkl_kursy')->get();

        foreach ($oldKursy as $oldKurs) {
            $walutaId = null;

            if (is_numeric($oldKurs->waluta)) {
                // Jeśli to ID, szukamy czy mamy taką walutę
                $waluta = Waluta::find($oldKurs->waluta);
                if ($waluta) {
                    $walutaId = $waluta->id;
                }
            } else {
                // Jeśli to nazwa (np. "PLN"), szukamy po nazwie
                $waluta = Waluta::where('name', strtoupper(trim($oldKurs->waluta)))->first();
                if ($waluta) {
                    $walutaId = $waluta->id;
                }
            }

            // Jeśli znaleźliśmy walutę, dodajemy kurs
            if ($walutaId) {
                Kursy::create([
                    'waluta_id' => $walutaId,
                    'kurs' => $oldKurs->kurs,
                    'user_id' => 1,
                    'created_at' => $oldKurs->time,
                ]);
            } else {
                // Log błędu dla Ciebie, żebyś widział co pominął
                echo "Pominięto kurs ID {$oldKurs->id} - nie znaleziono waluty: {$oldKurs->waluta}\n";
            }
        }
    }
}
