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

        // Sprawdzamy obie potencjalne tabele źródłowe
        $tables = ['mkl_kontaktKli', 'mkl_kontakty'];

        foreach ($tables as $tableName) {
            if (!Schema::connection('old_crm')->hasTable($tableName)) {
                continue;
            }

            $oldKontakts = DB::connection('old_crm')->table($tableName)->get();

            foreach ($oldKontakts as $old) {
                $clientId = null;
                $kontaktPersonId = null;
                $zapytaniaId = null;
                $id = null;

                if ($tableName === 'mkl_kontaktKli') {
                    $id = $old->id;
                    // W tej tabeli klientIdKon to BEZPOŚREDNIO ID klienta
                    $clientId = $old->klientIdKon;
                    $kontaktPersonId = null;

                    $callDate = $this->formatDate($old->lastKo);
                    $callTime = $old->time ? date('H:i:s', strtotime($old->time)) : '00:00:00';
                    $nextCallDate = $this->formatDate($old->nextKo);
                    $description = $old->opis ?? '';
                    $createdAt = $this->formatDate($old->time);
                    $subject = 'Kontakt z CRM (mkl_kontaktKli)';

                    $userIdRaw = $old->user;
                } else {
                    // Logika dla mkl_kontakty
                    $id = $old->idKo ?? $old->id;
                    $clientId = $old->klientIdKon ?? null;
                    $kontaktPersonId = is_numeric($old->klientKo) ? (int)$old->klientKo : null;

                    // Jeśli nie mamy clientId, a mamy osobę, spróbujmy wyciągnąć klienta z osoby
                    if (!$clientId && $kontaktPersonId) {
                        $clientId = DB::table('kontakt_persons')->where('id', $kontaktPersonId)->value('client_id');
                    }

                    $zapytaniaId = $old->zapytKo ?? $old->zap_id ?? null;
                    if ($zapytaniaId <= 0) $zapytaniaId = null;

                    $callDate = $this->formatDate($old->dataKo ?? $old->time ?? null);
                    $callTime = isset($old->time) ? date('H:i:s', strtotime($old->time)) : (isset($old->dataKo) ? '00:00:00' : null);
                    $nextCallDate = $this->formatDate($old->nextKo ?? null);
                    $description = $old->opisKo ?? ($old->opis ?? '');
                    $createdAt = $this->formatDate($old->rejestrKo ?? $old->time ?? null);
                    $subject = $old->tematKo ?? 'Kontakt z CRM';

                    $userIdRaw = $old->ktoKo ?? $old->user ?? 1;
                }

                // Jeśli nadal nie mamy clientId, pomijamy rekord
                if (!$clientId) {
                    continue;
                }

                // Mapowanie użytkownika
                $userId = 1;
                if (is_numeric($userIdRaw)) {
                    $userId = (int)$userIdRaw;
                } else if ($userIdRaw) {
                    $user = User::where(DB::raw("CONCAT(first_name, ' ', last_name)"), $userIdRaw)->first();
                    if ($user) $userId = $user->id;
                }

                Kontakt::updateOrCreate(
                    ['id' => $id],
                    [
                        'client_id' => $clientId,
                        'call_date' => $callDate,
                        'call_time' => $callTime,
                        'next_call_date' => $nextCallDate,
                        'next_call_time' => null,
                        'subject' => $subject,
                        'description' => $description,
                        'user_id' => $userId,
                        'kontakt_person_id' => $kontaktPersonId,
                        'zapytania_id' => $zapytaniaId,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]
                );
            }
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
