<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LegacyStronyWwwSeeder extends Seeder
{
    public function run()
    {
        DB::table('strony_wwws')->truncate();

        $oldLinks = DB::connection('old_crm')
            ->table('mkl_linkWww')
            ->get();

        foreach ($oldLinks as $old) {
            $createdAt = $this->formatDate($old->dateCreate);
            $updatedAt = $this->formatDate($old->lastCheck);

            DB::table('strony_wwws')->insert([
                'id'         => $old->id,
                'name'       => $old->nazwa,
                'link'       => $old->link,
                'click'      => $old->nrCheck,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
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
