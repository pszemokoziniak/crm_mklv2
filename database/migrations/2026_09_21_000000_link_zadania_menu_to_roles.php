<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class LinkZadaniaMenuToRoles extends Migration
{
    /**
     * Zakładka "Zadania" istnieje w menu, ale nie była powiązana z żadną rolą,
     * więc widzieli ją tylko super-admin/Administrator. Ten sam zestaw ról co
     * dla zakładek Zapytania/Oferty/Kontakty (Kierownictwo, Eksport).
     */
    private array $roles = ['Kierownictwo', 'Eksport'];

    public function up(): void
    {
        // Upewnij się, że pozycja menu istnieje (zgodnie z MainMenuSeeder).
        $menuId = DB::table('main_menus')->where('route', '/zadania')->value('id');

        if (!$menuId) {
            $menuId = DB::table('main_menus')->insertGetId([
                'name' => 'Zadania',
                'route' => '/zadania',
                'icon' => 'tasks',
                'order' => 6,
            ]);
        }

        foreach ($this->roles as $roleName) {
            $roleId = DB::table('roles')->where('name', $roleName)->value('id');

            if ($roleId) {
                DB::table('uprawnienia_main_menu')->insertOrIgnore([
                    'uprawnienia_id' => $roleId,
                    'main_menu_id' => $menuId,
                ]);
            }
        }
    }

    public function down(): void
    {
        $menuId = DB::table('main_menus')->where('route', '/zadania')->value('id');

        if (!$menuId) {
            return;
        }

        $roleIds = DB::table('roles')->whereIn('name', $this->roles)->pluck('id')->all();

        DB::table('uprawnienia_main_menu')
            ->where('main_menu_id', $menuId)
            ->whereIn('uprawnienia_id', $roleIds)
            ->delete();
    }
}
