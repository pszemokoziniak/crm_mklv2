<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class LegacyUserSeeder extends Seeder
{
    public function run()
    {
        $oldUsers = DB::connection('old_crm')->table('mkl_members')->get();

        foreach ($oldUsers as $oldUser) {
            // Rozdzielanie imienia i nazwiska
            $nameParts = explode(' ', $oldUser->userNa, 2);
            $firstName = $nameParts[0] ?? 'N/A';
            $lastName = $nameParts[1] ?? 'N/A';

            User::updateOrCreate(
                ['email' => $oldUser->email],
                [
                    'account_id' => 1, // Domyślne account_id
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'password' => $oldUser->password, // Kopiujemy hash
                    'active' => ($oldUser->status == 0) ? 1 : 0, // Zakładam, że 0 w starej bazie to aktywny
                    'owner' => ($oldUser->level == 1) ? true : false, // Przykładowe mapowanie uprawnień
                ]
            );
        }
    }
}
