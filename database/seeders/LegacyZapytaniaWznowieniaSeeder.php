<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LegacyZapytaniaWznowieniaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('zapytania_wznowienias')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // fetch old data
        $oldWznowienia = DB::connection('old_crm')->table('mkl_refreshZap')->get();
        $availableZapytaniaIds = DB::table('zapytanias')->pluck('id')->toArray();
        $availableUserIds = DB::table('users')->pluck('id')->toArray();

        $newData = [];

        foreach ($oldWznowienia as $old) {
            $idZapytania = (int)$old->tag;
            $idUser = $old->ktoWzno ? (int)$old->ktoWzno : null;

            if (!in_array($idZapytania, $availableZapytaniaIds)) {
                continue; // Skip if corresponding Zapytania doesn't exist
            }

            if ($idUser !== null && !in_array($idUser, $availableUserIds)) {
                $idUser = null; // or perhaps set to a default user, for now let's set to null if not found
            }

            $newData[] = [
                'id'           => $old->id,
                'text'         => $old->data,
                'id_zapytania' => $idZapytania,
                'id_user'      => $idUser,
                'time'         => $old->time ?: now(),
            ];
        }

        // Insert chunks to avoid memory limit issues and query size limits
        $chunks = array_chunk($newData, 500);
        foreach ($chunks as $chunk) {
            DB::table('zapytania_wznowienias')->insert($chunk);
        }
    }
}
