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

        $oldKraje = DB::connection('old_crm')->select("SELECT * FROM mkl_kraje");

        foreach ($oldKraje as $oldKraj) {
            $data = (array)$oldKraj;

            $id = $data['idWal'] ?? null;
            $name = $data['kraj'] ?? null;

            if ($name) {
                // Próba naprawy kodowania z UTF-8 (jeśli błędnie odczytane jako ISO) lub bezpośrednio z ISO-8859-2
                // "Ĺotwa" sugeruje, że UTF-8 zostało zinterpretowane jako Windows-1252/ISO-8859-1
                // Najpierw spróbujmy mb_convert_encoding dla typowych problemów z polskimi znakami

                if (mb_detect_encoding($name, 'UTF-8', true) === false) {
                    $name = iconv('ISO-8859-2', 'UTF-8//IGNORE', $name);
                } else {
                    // Jeśli to "Ĺotwa", to znaczy że mamy podwójne kodowanie (UTF8 odczytane jako ISO i znów do UTF8)
                    // Spróbujmy to odkręcić:
                    $converted = mb_convert_encoding($name, 'ISO-8859-1', 'UTF-8');
                    if (mb_detect_encoding($converted, 'UTF-8', true) === false) {
                         $name = mb_convert_encoding($converted, 'UTF-8', 'ISO-8859-2');
                    }
                }

                DB::table('krajs')->insert([
                    'id'         => $id,
                    'name'       => trim($name),
                    'waluta_id'  => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        echo "Wstawiono " . DB::table('krajs')->count() . " krajów.\n";
    }
}
