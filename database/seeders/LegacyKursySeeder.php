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
            $waluta = Waluta::where('name', $oldKurs->waluta)->first();

            if (!$waluta) {
                // Jeśli nie ma waluty, stwórzmy ją w locie
                $waluta = Waluta::create(['name' => $oldKurs->waluta, 'user_id' => 1]);
            }

            Kursy::create([
                'waluta_id' => $waluta->id,
                'kurs' => $oldKurs->kurs,
                'user_id' => 1, // Domyślny admin
                'created_at' => $oldKurs->time,
            ]);
        }
    }
}
