<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RemoveMenuFromMainMenus extends Migration
{
    public function up()
    {
        // Usuń powiązania z tabeli pivot
        $menuId = DB::table('main_menus')->where('route', '/menu')->value('id');

        if ($menuId) {
            DB::table('uprawnienia_main_menu')->where('main_menu_id', $menuId)->delete();
            DB::table('main_menus')->where('id', $menuId)->delete();
        }
    }

    public function down()
    {
        DB::table('main_menus')->insert([
            'name' => 'Menu',
            'route' => '/menu',
            'icon' => 'menu',
            'order' => 15,
        ]);
    }
}
