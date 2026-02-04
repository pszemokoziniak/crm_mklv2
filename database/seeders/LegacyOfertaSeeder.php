<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LegacyOfertaSeeder extends Seeder
{
    public function run()
    {
        // Wyłączamy klucze obce na czas całego procesu
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('ofertas')->truncate();

        $oldOferty = DB::connection('old_crm')->table('mkl_oferty')->get();
        $availableUserIds = DB::table('users')->pluck('id')->toArray();

        foreach ($oldOferty as $old) {
            $walutaId = DB::table('walutas')->where('name', $old->waluta_oferta)->value('id');

            // Mapowanie statusów
            $statusId = $old->statusId_oferta;
            if (!DB::table('oferta_statuses')->where('id', $statusId)->exists()) {
                $statusId = DB::table('oferta_statuses')->where('name', $old->status_oferta)->value('id');
                if (!$statusId) {
                    $statusId = DB::table('oferta_statuses')->insertGetId([
                        'id' => $old->statusId_oferta,
                        'name' => $old->status_oferta ?: 'Nieznany',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Walidacja user_id
            $userId = is_numeric($old->dodal) ? (int)$old->dodal : 1;
            if (!in_array($userId, $availableUserIds)) {
                $userId = 1;
            }

            $archUserId = is_numeric($old->arch_user) ? (int)$old->arch_user : null;
            if ($archUserId && !in_array($archUserId, $availableUserIds)) {
                $archUserId = null;
            }

            DB::table('ofertas')->insert([
                'id'                => $old->id,
                'typ'               => $old->tyoe_oferta ?: 'Brak',
                'zapytania_id'      => $old->nazwa_zap, // Mapowanie: nazwa_zap => zapytania_id
                'client_id'         => $old->klient_oferta, // Mapowanie: klient_oferta => client_id
                'data_wyslania'     => $this->formatDate($old->wyslanie_oferta),
                'kwota'             => $old->wartosc_oferta,
                'waluta_id'         => $walutaId ?: 1,
                'kurs'              => $old->kursWal,
                'kwotaPLN'          => $old->kwotaPLN,
                'data_kontakt'      => $this->formatDate($old->arch_k_time),
                'oferta_status_id'  => $statusId,
                'opis'              => $old->opis_oferta,
                'arch_user_id'      => $archUserId,
                'arch_text'         => $old->arch_note,
                'arch_time'         => $this->formatDate($old->arch_time),
                'user_id'           => $userId,
                'created_at'        => $this->formatDate($old->rejestr_oferta),
                'updated_at'        => $this->formatDate($old->rejestr_oferta),
            ]);
        }

        // Włączamy klucze obce z powrotem
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    private function formatDate($date)
    {
        if (!$date || $date == '0000-00-00' || $date == '0000-00-00 00:00:00') {
            return null;
        }
        return $date;
    }
}
