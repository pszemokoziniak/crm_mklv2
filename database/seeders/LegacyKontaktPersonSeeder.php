<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\KontaktPerson;

class LegacyKontaktPersonSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        $oldOsoby = DB::connection('old_crm')->table('mkl_osoby')->get();
        $availableClientIds = DB::table('clients')->pluck('id')->toArray();

        foreach ($oldOsoby as $old) {
            // Mapowanie user_id (dodal)
            $userId = is_numeric($old->dodal) ? (int)$old->dodal : 1;

            // Sprawdzamy czy klient istnieje, jeśli nie - możemy przypisać do jakiegoś domyślnego lub zostawić (przy wyłączonych FK przejdzie)
            // Ale lepiej wiedzieć, że brakuje klienta.

            KontaktPerson::updateOrCreate(
                ['id' => $old->idOs],
                [
                    'first_name'  => $old->imie_kontakt,
                    'last_name'   => $old->nazwisko_kontakt,
                    'position'    => $old->stanowisko_kontakt,
                    'phone1'      => $old->tel1_kontakt,
                    'phone2'      => $old->tel2_kontakt,
                    'email'       => $old->email_kontakt,
                    'miasto'      => $old->miastoOs,
                    'client_id'   => $old->klientOsoby,
                    'description' => '',
                    'user_id'     => $userId,
                    'created_at'  => $this->formatDate($old->rejestr_klient),
                    'updated_at'  => $this->formatDate($old->rejestr_klient),
                ]
            );
        }

        Schema::enableForeignKeyConstraints();
    }

    private function formatDate($date)
    {
        if (!$date || $date == '0000-00-00' || $date == '0000-00-00 00:00:00') {
            return null;
        }
        return $date;
    }
}
