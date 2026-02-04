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

            // Sprawdzamy czy kraj_id istnieje w nowej tabeli krajs
            $krajId = in_array($old->kraj_zap, $availableKrajIds) ? $old->kraj_zap : 1;

            // Mapujemy zakres_zap 1:1 na zakres_id, sprawdzając czy istnieje
            $zakresId = in_array($old->zakres_zap, $availableZakresIds) ? $old->zakres_zap : 1;

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
                'user_opracowuje_id' => $old->otrzymal_zap ?: ($old->rejestUser ?: 1),
                'start'              => $this->formatDate($old->start_zap),
                'end'                => $this->formatDate($old->end_zap),
                'kwota'              => $old->kwota_zap,
                'waluta_id'          => $walutaId,
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
