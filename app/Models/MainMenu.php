<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainMenu extends Model
{

    protected $fillable = [
        'name',
        'route',
        'icon',
        'order', // Optional: for ordering menu items
    ];

    /**
     * The roles that belong to the MainMenu
     */
    public function uprawnienia()
    {
        return $this->belongsToMany(Uprawnienia::class, 'uprawnienia_main_menu', 'main_menu_id', 'uprawnienia_id');
    }
}
