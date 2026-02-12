<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LegacyZapytaniaSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('zapytanias')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $oldZapytania = DB::connection('old_crm')->table('mkl_zapytania')->get();
        $availableKrajIds = DB::table('krajs')->pluck('id')->toArray();
        $availableZakresIds = DB::table('zakres')->pluck('id')->toArray();

        foreach ($oldZapytania as $old) {
            $walutaId = DB::table('walutas')->where('name', $old->waluta_zap)->value('id');

            // Mapowanie kraj_zap na kraj_id
            $krajId = null;
            if ($old->kraj_zap) {
                // Najpierw szukamy po nazwie
                $krajId = DB::table('krajs')->where('name', trim($old->kraj_zap))->value('id');

                // Jeśli nie znaleziono, a wartość jest liczbą, sprawdzamy czy to ID
                if (!$krajId && is_numeric($old->kraj_zap)) {
                    if (in_array((int)$old->kraj_zap, $availableKrajIds)) {
                        $krajId = (int)$old->kraj_zap;
                    }
                }
            }
            $krajId = $krajId ?: 1; // Fallback do ID 1

            // Mapujemy zakres_zap na zakres_id
            $zakresId = null;
            if ($old->zakres_zap) {
                $zakresId = DB::table('zakres')->where('name', trim($old->zakres_zap))->value('id');

                if (!$zakresId && is_numeric($old->zakres_zap)) {
                    if (in_array((int)$old->zakres_zap, $availableZakresIds)) {
                        $zakresId = (int)$old->zakres_zap;
                    }
                }
            }
            $zakresId = $zakresId ?: 1;

            DB::table('zapytanias')->insert([
                'id'                 => $old->id,
                'id_zapyt'           => $old->id_zapyt,
                'user_otrzymal_id'   => $old->otrzymal_zap ?: ($old->rejestUser ?: 1),
                'data_otrzymania'    => $this->formatDate($old->data_zap),
                'data_zlozenia'      => $this->formatDate($old->planowane_zap),
                'client_id'          => $old->firma_zap,
                'nazwa_projektu'     => $old->nazwa_zap,
                'preliminarz'        => null,
                'miejscowosc'        => Str::limit($old->city_zap, 100, ''),
                'kraj_id'            => $krajId,
                'zakres_id'          => $zakresId,
                'user_opracowuje_id' => (int)$old->osoba_zap ?: ($old->rejestUser ?: 1),
                'start'              => $this->formatDate($old->start_zap),
                'end'              => $this->formatDate($old->end_zap),
                'kwota'              => $old->kwota_zap,
                'waluta_id'          => $walutaId ?: 1,
                'opis'               => $old->opis_zap,
                'miasto'             => Str::limit($old->city_zap, 50, ''),
                'user_id'            => $old->rejestUser ?: 1,
                'kurs'               => $old->kursWal,
                'kwotaPLN'           => $old->kwotaPLN,
                'arch'               => $old->arch_zloz,
                'arch_time'          => $this->formatDate($old->arch_z_time),
                'arch_user'          => $old->arch_z_user,
                'wznowienie'         => $old->zap_wznowienie,
                'created_at'         => $this->formatDate($old->restr_zap),
                'updated_at'         => $this->formatDate($old->modTime),
            ]);
        }
    }

    private function formatDate($date)
    {
        if (!$date || $date == '0000-00-00' || $date == '0000-00-00 00:00:00') {
            return null;
        }
        return $date;
    }
}
