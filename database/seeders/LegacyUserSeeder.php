<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LegacyUserSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->delete(); // Używamy delete zamiast truncate dla pewności
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $oldUsers = DB::connection('old_crm')->table('mkl_members')->get();

        foreach ($oldUsers as $oldUser) {
            $nameParts = explode(' ', $oldUser->userNa, 2);
            $firstName = $nameParts[0] ?? 'N/A';
            $lastName = $nameParts[1] ?? 'N/A';

            // Używamy updateOrInsert zamiast insert
            DB::table('users')->updateOrInsert(
                ['id' => $oldUser->id],
                [
                    'account_id' => 1,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $oldUser->email,
                    'password' => $oldUser->password,
                    'active' => ($oldUser->status == 0) ? 1 : 0,
                    'owner' => ($oldUser->level == 1) ? true : false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
