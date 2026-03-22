<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
class Uprawnienia extends SpatieRole
{

    protected $fillable = [
        'name',
        'description', // If you want to use description, ensure your roles table has this column
    ];

    /**
     * The main menus that belong to the Uprawnienia (Role)
     */
    public function mainMenus()
    {
        return $this->belongsToMany(MainMenu::class, 'uprawnienia_main_menu', 'uprawnienia_id', 'main_menu_id');
    }
}
