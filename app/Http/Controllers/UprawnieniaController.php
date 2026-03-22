<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditRequest;
use App\Models\Uprawnienia; // Używamy naszego modelu Uprawnienia
use App\Models\MainMenu; // Importujemy model MainMenu
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class UprawnieniaController extends Controller
{
    public function index()
    {
        return Inertia::render('Uprawnienia/Index', [
            'uprawnienia' => Uprawnienia::with('mainMenus') // Eager load mainMenus
                ->get()
                ->map(function ($uprawnienie) {
                    return [
                        'id' => $uprawnienie->id,
                        'name' => $uprawnienie->name,
                        'main_menus' => $uprawnienie->mainMenus->map(fn($menu) => $menu->only('id', 'name')),
                    ];
                }),
        ]);
    }

    public function create()
    {
        return Inertia::render('Uprawnienia/Create');
    }

    public function store(EditRequest $request)
    {
        Uprawnienia::create(['name' => $request->validated('name')]);

        return Redirect::route('uprawnienia')->with('success', 'Uprawnienie dodane.');
    }

    public function edit(Uprawnienia $uprawnienia) // Zmieniono typ na Uprawnienia
    {
        return Inertia::render('Uprawnienia/Edit', [
            'uprawnienia' => [
                'id' => $uprawnienia->id,
                'name' => $uprawnienia->name,
                'main_menus' => $uprawnienia->mainMenus->pluck('id'), // Tylko ID przypisanych menu
            ],
            'allMainMenus' => MainMenu::orderBy('order')->get()->map(function ($menu) {
                return [
                    'id' => $menu->id,
                    'name' => $menu->name,
                    'route' => $menu->route,
                    'icon' => $menu->icon,
                ];
            }),
        ]);
    }

    public function update(EditRequest $request, Uprawnienia $uprawnienia) // Zmieniono typ na Uprawnienia
    {
        $uprawnienia->update(['name' => $request->validated('name')]);

        return Redirect::route('uprawnienia')->with('success', 'Poprawiono.');
    }

    public function destroy(Uprawnienia $uprawnienia) // Zmieniono typ na Uprawnienia
    {
        $uprawnienia->delete();

        return Redirect::route('uprawnienia')->with('success', 'Usunięto.');
    }

    public function restore(Uprawnienia $uprawnienia)
    {
        return Redirect::back()->with('error', 'Przywracanie uprawnień nie jest obecnie wspierane.');
    }

    public function syncMainMenus(Request $request, Uprawnienia $uprawnienia)
    {
        $uprawnienia->mainMenus()->sync($request->input('main_menu_ids', []));

        return Redirect::back()->with('success', 'Przypisano elementy menu.');
    }
}
