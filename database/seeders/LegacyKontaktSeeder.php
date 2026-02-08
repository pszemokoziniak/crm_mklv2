<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Kontakt;
use App\Models\User;

class LegacyKontaktSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        $tableName = 'mkl_kontakty';
        if (!Schema::connection('old_crm')->hasTable($tableName)) {
            $tableName = 'mkl_kontaktKli';
        }

        $oldKontakts = DB::connection('old_crm')->table($tableName)->get();
        $availableKontaktPersonIds = DB::table('kontakt_persons')->pluck('id')->toArray();
        $availableClientIds = DB::table('clients')->pluck('id')->toArray();
        $availableZapytaniaIds = DB::table('zapytanias')->pluck('id')->toArray();

        foreach ($oldKontakts as $old) {
            // 1. Mapowanie ID klienta
            $clientId = $old->klientIdKon ?? $old->klientKo ?? null;

            if ($clientId && !is_numeric($clientId)) {
                $clientId = DB::table('clients')->where('nazwa', $clientId)->value('id');
            }

            // Jeśli klientKo to ID osoby kontaktowej, spróbujmy wyciągnąć klienta z tej osoby
            if ((!$clientId || !in_array($clientId, $availableClientIds)) && is_numeric($old->klientKo)) {
                $clientId = DB::table('kontakt_persons')->where('id', $old->klientKo)->value('client_id');
            }

            if (!$clientId || !in_array($clientId, $availableClientIds)) {
                continue;
            }

            // 2. Mapowanie osoby kontaktowej (klientKo -> kontakt_person_id)
            $kontaktPersonId = is_numeric($old->klientKo) ? (int)$old->klientKo : null;

            if ($kontaktPersonId && !in_array($kontaktPersonId, $availableKontaktPersonIds)) {
                $kontaktPersonId = null;
            }

            // 3. Mapowanie zapytania (zapytKo) - obsługa wartości -1
            $zapytaniaId = $old->zapytKo ?? $old->zap_id ?? null;
            if ($zapytaniaId <= 0 || !in_array($zapytaniaId, $availableZapytaniaIds)) {
                $zapytaniaId = null;
            }

            // 4. Mapowanie użytkownika
            $userId = $old->ktoKo ?? $old->user ?? 1;
            if (!is_numeric($userId)) {
                $user = User::where(DB::raw("CONCAT(first_name, ' ', last_name)"), $userId)->first();
                $userId = $user ? $user->id : 1;
            }

            Kontakt::updateOrCreate(
                ['id' => $old->idKo ?? $old->id],
                [
                    'client_id' => $clientId,
                    'call_date' => $this->formatDate($old->dataKo ?? $old->time ?? null),
                    'call_time' => isset($old->time) ? date('H:i:s', strtotime($old->time)) : (isset($old->dataKo) ? '00:00:00' : null),
                    'next_call_date' => $this->formatDate($old->nextKo ?? null),
                    'next_call_time' => null,
                    'subject' => $old->tematKo ?? 'Kontakt z CRM',
                    'description' => $old->opisKo ?? ($old->opis ?? ''),
                    'user_id' => $userId,
                    'kontakt_person_id' => $kontaktPersonId,
                    'zapytania_id' => $zapytaniaId,
                    'created_at' => $this->formatDate($old->rejestrKo ?? $old->time ?? null),
                    'updated_at' => $this->formatDate($old->rejestrKo ?? $old->time ?? null),
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
