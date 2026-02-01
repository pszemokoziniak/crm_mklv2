<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Client;
use App\Models\User;

class LegacyClientSeeder extends Seeder
{
    public function run()
    {
        $oldClients = DB::connection('old_crm')->table('mkl_klienci')->get();

        foreach ($oldClients as $oldClient) {
            // 1. Mapowanie użytkownika
            $user = User::where('email', $oldClient->dodal_klient)->first();
            $userId = $user ? $user->id : 1; // Jeśli nie znaleziono, przypisz do pierwszego (wymagane)

            // 2. Mapowanie kraju i branży (kopiujemy ID 1 do 1)
            $krajId = $oldClient->kraj_firmy ?: 1; // Jeśli puste, dajemy 1 (wymagane)
            $branzaId = $oldClient->branza_firmy ?: 1; // Jeśli puste, dajemy 1 (wymagane)

            Client::updateOrCreate(
                ['id' => $oldClient->id],
                [
                    'nazwa' => $oldClient->nazwa_firmy,
                    'ulica' => $oldClient->ulica_firmy,
                    'miasto' => $oldClient->miasto_firmy,
                    'www' => $oldClient->www_firmy,
                    'linkedIn' => $oldClient->linkedIn_firmy,
                    'message' => $oldClient->message_kontakt,
                    'user_id' => $userId,
                    'kraj_id' => $krajId,
                    'branza_id' => $branzaId,
                    'created_at' => $oldClient->rejestr_klient,
                    'updated_at' => $oldClient->rejestr_klient,
                ]
            );
        }
    }
}
