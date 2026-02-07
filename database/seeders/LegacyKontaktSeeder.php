<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Kontakt;
use App\Models\User;
use App\Models\Client;

class LegacyKontaktSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        $oldKontakts = DB::connection('old_crm')->table('mkl_kontaktKli')->get();

        foreach ($oldKontakts as $oldKontakt) {
            // Mapowanie użytkownika po nazwie (stara baza ma varchar w polu user)
            $user = User::where(DB::raw("CONCAT(first_name, ' ', last_name)"), $oldKontakt->user)->first();
            $userId = $user ? $user->id : 1;

            // Wyciąganie godziny z timestampa 'time'
            $callTime = date('H:i:s', strtotime($oldKontakt->time));

            // Obsługa nieprawidłowej daty '0000-00-00'
            $callDate = $oldKontakt->nextKo;
            if ($callDate === '0000-00-00' || empty($callDate)) {
                $callDate = null;
            }

            Kontakt::updateOrCreate(
                ['id' => $oldKontakt->id],
                [
                    'client_id' => $oldKontakt->klientIdKon,
                    'call_date' => $callDate,
                    'call_time' => $callTime,
                    'subject' => 'Imported from old CRM',
                    'description' => $oldKontakt->opis,
                    'user_id' => $userId,
                    'kontakt_id' => $oldKontakt->klientIdKon, // Przeniesienie klientIdKon do kontakt_id
                    'zapytania_id' => null,
                    'created_at' => $oldKontakt->time,
                    'updated_at' => $oldKontakt->time,
                ]
            );
        }

        Schema::enableForeignKeyConstraints();
    }
}
