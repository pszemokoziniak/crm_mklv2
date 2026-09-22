<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddZgloszeniaMenuItem extends Migration
{
    /**
     * Nowa pozycja menu „Zgłoszenia" (/zgloszenia) widoczna dla ról,
     * które testują aplikację: Kierownictwo, Eksport, Techniczny.
     * super-admin/Administrator widzą wszystko niezależnie od powiązań.
     */
    private array $roles = ['Kierownictwo', 'Eksport', 'Techniczny'];

    public function up(): void
    {
        $menuId = DB::table('main_menus')->where('route', '/zgloszenia')->value('id');

        if (!$menuId) {
            $menuId = DB::table('main_menus')->insertGetId([
                'name' => 'Zgłoszenia',
                'route' => '/zgloszenia',
                'icon' => 'zgloszenia',
                'order' => 6, // tuż za „Zadania" (order 6, niższe id)
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
        $menuId = DB::table('main_menus')->where('route', '/zgloszenia')->value('id');

        if (!$menuId) {
            return;
        }

        DB::table('uprawnienia_main_menu')->where('main_menu_id', $menuId)->delete();
        DB::table('main_menus')->where('id', $menuId)->delete();
    }
}
