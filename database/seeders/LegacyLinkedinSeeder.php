<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LegacyLinkedinSeeder extends Seeder
{
    public function run()
    {
        DB::table('linkedins')->truncate();

        $oldLinkedins = DB::connection('old_crm')
            ->table('mkl_linkedIn')
            ->join('mkl_klienci', 'mkl_linkedIn.idKlient', '=', 'mkl_klienci.id')
            ->select(
                'mkl_linkedIn.*',
                'mkl_klienci.linkedIn_firmy',
                'mkl_klienci.dodal_klient'
            )
            ->get();

        foreach ($oldLinkedins as $old) {
            if (empty($old->linkedIn_firmy)) {
                continue;
            }

            $date = $this->formatDate($old->lastCheck);

            DB::table('linkedins')->insert([
                'id'         => $old->id,
                'client_id'  => $old->idKlient,
                'user_id'    => $old->dodal_klient ?: 1,
                'link'       => $old->linkedIn_firmy,
                'click'      => $old->nrCheck,
                'created_at' => $date,
                'updated_at' => $date,
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
