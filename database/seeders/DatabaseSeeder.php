<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Contact;
use App\Models\Organization;
use App\Models\User;
use App\Models\Zakres;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $account = Account::create(['name' => 'MKL Spółka z o.o.']);

        User::factory()->create([
            'account_id' => $account->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@example.com',
            'password' => 'secret',
            'owner' => true,
            'active' => 1,
        ]);

        User::factory(5)->create(['account_id' => $account->id, 'active' => 0]);

//        $organizations = Organization::factory(100)
//            ->create(['account_id' => $account->id]);

//        Contact::factory(100)
//            ->create(['account_id' => $account->id])
//            ->each(function ($contact) use ($organizations) {
//                $contact->update(['organization_id' => $organizations->random()->id]);
//            });

        $this->call([
            DataSeeder::class,
        ]);
    }
}
