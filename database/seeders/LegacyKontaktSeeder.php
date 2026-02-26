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
        // Wyłączamy klucze obce na czas importu
        Schema::disableForeignKeyConstraints();

        $tableName = 'mkl_kontakty';

        if (!Schema::connection('old_crm')->hasTable($tableName)) {
            $this->command->error("Tabela {$tableName} nie istnieje w połączeniu old_crm!");
            return;
        }

        $oldKontakts = DB::connection('old_crm')->table($tableName)->get();

        foreach ($oldKontakts as $old) {
            // Mapowanie ID klienta ze starego pola klientKo
            $clientId = (int)$old->klientKo;

            if (!$clientId) {
                $this->command->warn("Pominięto rekord ID: {$old->idKo} - brak client_id");
                continue;
            }

            // Mapowanie użytkownika (ktoKo)
            $userId = (int)($old->ktoKo ?: 1);

            // Obsługa ID oferty i zapytania (0 traktujemy jako null)
            $ofertaId = ($old->ofertaKo > 0) ? $old->ofertaKo : null;
            $zapytaniaId = ($old->zapytKo > 0) ? $old->zapytKo : null;

            Kontakt::updateOrCreate(
                ['id' => $old->idKo],
                [
                    'client_id'         => $clientId,
                    'call_date'         => $this->formatDate($old->dataKo),
                    'call_time'         => '00:00:00', // W starej tabeli brało tylko DATE
                    'next_call_date'    => $this->formatDate($old->nextKo),
                    'next_call_time'    => null,
                    'subject'           => $old->tematKo ?? 'Kontakt z CRM',
                    'description'       => $old->opisKo ?? '',
                    'user_id'           => $userId,
                    'opiekun_id'        => null, // Jeśli masz logikę opiekuna, można tu dodać
                    'kontakt_person_id' => null, // Tabela źródłowa nie posiada jasnego powiązania z osobą kontaktową
                    'zapytania_id'      => $zapytaniaId,
                    'oferta_id'         => $ofertaId,
                    'created_at'        => $this->formatDate($old->rejestrKo),
                    'updated_at'        => $this->formatDate($old->rejestrKo),
                ]
            );
        }

        Schema::enableForeignKeyConstraints();
        $this->command->info("Import kontaktów zakończony sukcesem.");
    }

    /**
     * Formatuje datę, unikając błędnych wartości MySQL
     */
    private function formatDate($date)
    {
        if (!$date || $date == '0000-00-00' || $date == '0000-00-00 00:00:00') {
            return null;
        }
        return $date;
    }
}
