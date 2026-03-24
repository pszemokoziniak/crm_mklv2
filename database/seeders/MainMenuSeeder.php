<?php

namespace Database\Seeders;

use App\Models\MainMenu;
use Illuminate\Database\Seeder;

class MainMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $menuItems = [
            [
                'name' => 'Home',
                'route' => '/',
                'icon' => 'home',
                'order' => 1,
            ],
            [
                'name' => 'Klienci',
                'route' => '/clients',
                'icon' => 'clients',
                'order' => 2,
            ],
            [
                'name' => 'Zapytania',
                'route' => '/zapytania',
                'icon' => 'zapytania',
                'order' => 3,
            ],
            [
                'name' => 'Oferty',
                'route' => '/oferta',
                'icon' => 'oferty',
                'order' => 4,
            ],
            [
                'name' => 'Kontakty',
                'route' => '/kontakt',
                'icon' => 'contact',
                'order' => 5,
            ],
            [
                'name' => 'Zadania',
                'route' => '/zadania',
                'icon' => 'tasks',
                'order' => 6,
            ],
            [
                'name' => 'Kalendarz',
                'route' => '/calendar',
                'icon' => 'calendar',
                'order' => 7,
            ],
            [
                'name' => 'Przyszłe projekty',
                'route' => '/futureproject',
                'icon' => 'future',
                'order' => 8,
            ],
            [
                'name' => 'LinkedIn',
                'route' => '/linkedin',
                'icon' => 'linkedin',
                'order' => 9,
            ],
            [
                'name' => 'Linki www',
                'route' => '/stronywww',
                'icon' => 'www',
                'order' => 10,
            ],
            [
                'name' => 'Statystyki',
                'route' => '/stats',
                'icon' => 'statystyki',
                'order' => 11,
            ],
            [
                'name' => 'Ustawienia',
                'route' => '/edit',
                'icon' => 'edit',
                'order' => 12,
            ],
            [
                'name' => 'Użytkownicy',
                'route' => '/users',
                'icon' => 'users',
                'order' => 13,
            ],
            [
                'name' => 'Historia',
                'route' => '/activity',
                'icon' => 'historia',
                'order' => 14,
            ],
            [
                'name' => 'Menu',
                'route' => '/menu',
                'icon' => 'menu',
                'order' => 15,
            ],
        ];

        foreach ($menuItems as $item) {
            MainMenu::firstOrCreate(
                ['route' => $item['route']], // Unique identifier to prevent duplicates
                $item
            );
        }
    }
}
