<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\FutureProject;
use App\Models\Kraj;
use App\Models\Client;
use App\Models\Objekt;
use App\Models\Faza;
use App\Models\User;

class LegacyFutureProjectSeeder extends Seeder
{
    public function run()
    {
        $oldProjects = DB::connection('old_crm')->table('mkl_futureProjects')->get();

        // Pobieramy pierwsze dostępne ID dla fallbacków, aby uniknąć błędów kluczy obcych
        $defaultKrajId = Kraj::first()?->id ?? 1;
        $defaultClientId = Client::first()?->id;
        $defaultObjektId = Objekt::first()?->id ?? 1;
        $defaultFazaId = Faza::first()?->id ?? 1;
        $defaultUserId = User::first()?->id ?? 1;

        if (!$defaultClientId) {
            $this->command->error('Tabela clients jest pusta! Uruchom najpierw LegacyClientSeeder.');
            return;
        }

        foreach ($oldProjects as $oldProject) {
            // Kraj mapping
            $krajId = null;
            if (is_numeric($oldProject->kraj_projektu)) {
                $krajId = $oldProject->kraj_projektu;
            } else if (!empty($oldProject->kraj_projektu)) {
                $krajName = trim(iconv('ISO-8859-2', 'UTF-8//IGNORE', $oldProject->kraj_projektu));
                $kraj = Kraj::where('name', $krajName)->first();
                if ($kraj) {
                    $krajId = $kraj->id;
                }
            }

            // Sprawdzanie czy powiązane rekordy istnieją
            $finalKrajId = Kraj::find($krajId) ? $krajId : $defaultKrajId;
            $finalClientId = Client::find($oldProject->wykonawca_projektu) ? $oldProject->wykonawca_projektu : $defaultClientId;
            $finalObjektId = Objekt::find($oldProject->rodzaj_obiektu) ? $oldProject->rodzaj_obiektu : $defaultObjektId;
            $finalFazaId = Faza::find($oldProject->faza_projekt) ? $oldProject->faza_projekt : $defaultFazaId;
            $finalUserId = User::find($oldProject->id_user) ? $oldProject->id_user : $defaultUserId;

            FutureProject::updateOrCreate(
                ['id' => $oldProject->id],
                [
                    'nazwa'           => $oldProject->nazwa_projektu,
                    'miasto'          => $oldProject->miasto_projektu,
                    'kraj_id'         => $finalKrajId,
                    'objekt_id'       => $finalObjektId,
                    'client_id'       => $finalClientId,
                    'opis'            => $oldProject->opis_projektu,
                    'start'           => $this->formatDate($oldProject->start_projektu),
                    'end'             => $this->formatDate($oldProject->end_projektu),
                    'faza_id'         => $finalFazaId,
                    'inwestor'        => $oldProject->inwestor_projektu,
                    'dane_kontaktowe' => $oldProject->dane_kontakt_projektu,
                    'data_kontakt'    => $this->formatDate($oldProject->data_kontakt_projekt),
                    'user_id'         => $finalUserId,
                    'arch'            => $oldProject->arch_future,
                    'arch_time'       => $this->formatDate($oldProject->arch_time),
                    'arch_user'       => $oldProject->arch_user,
                    'created_at'      => $oldProject->input_date_project,
                    'updated_at'      => $oldProject->input_date_project,
                ]
            );
        }
    }

    private function formatDate($date)
    {
        if (!$date || $date == '0000-00-00') {
            return null;
        }
        return $date;
    }
}
