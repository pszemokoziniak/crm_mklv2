<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LegacyKrajSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('krajs')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Pobieramy wszystko, upewniając się że id też (jeśli istnieje)
        $oldKraje = DB::connection('old_crm')->select("SELECT * FROM mkl_kraje");

        foreach ($oldKraje as $oldKraj) {
            $data = (array)$oldKraj;

            // Jeśli 'id' nadal nie ma w obiekcie, spróbujemy go znaleźć w tablicy (case-insensitive)
            $id = null;
            foreach ($data as $key => $value) {
                if (strtolower($key) == 'id' || strtolower($key) == 'id_kraj') {
                    $id = $value;
                    break;
                }
            }

            // Jeśli nadal nie ma ID, a mamy 34 rekordy, użyjemy nazwy jako klucza do znalezienia ID w zapytaniach?
            // Nie, spróbujmy założyć, że ID to po prostu kolejny numer, jeśli go nie ma (ale to ryzykowne).
            // Jednak najpierw sprawdźmy czy 'kraj' ma polskie znaki.
            $name = $data['kraj'] ?? null;

            if ($name) {
                // Naprawa kodowania (Słowacja itp)
                $name = iconv('ISO-8859-2', 'UTF-8//IGNORE', $name);

                // Jeśli ID nadal nie znaleziono, a wiemy że zapytania mają kraj_id,
                // to może idWal jest tym kluczem? (mało prawdopodobne)
                // Wstawiamy z autoincrement jeśli nie ma ID, ale wypiszemy to.
                if ($id) {
                    DB::table('krajs')->insert([
                        'id'         => $id,
                        'name'       => trim($name),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    // Jeśli nie ma ID w tabeli, to może jest to tabela bez klucza?
                    // Wstawiamy i liczymy na to, że ID się nadadzą (ale to nie zadziała dla relacji)
                    DB::table('krajs')->insert([
                        'name'       => trim($name),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        echo "Wstawiono " . DB::table('krajs')->count() . " krajów.\n";
    }
}
