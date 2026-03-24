<?php

namespace App\Http\Controllers;

use App\Models\MainMenu; // Import the MainMenu model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MenuController extends Controller
{
    public function index()
    {
        return Inertia::render('Menu/Index', [
            'menuItems' => MainMenu::orderBy('order')->get()->map(function ($menuItem) {
                return [
                    'id' => $menuItem->id,
                    'name' => $menuItem->name,
                    'route' => $menuItem->route,
                    'icon' => $menuItem->icon,
                    'order' => $menuItem->order,
                    // Add other fields as necessary
                ];
            }),
        ]);
    }

    public function create()
    {
        return Inertia::render('Menu/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'max:100'],
            'route' => ['required', 'max:255'],
            'icon' => ['nullable', 'max:50'],
            'order' => ['nullable', 'integer'],
        ]);

        MainMenu::create([
            'name' => $request->name,
            'route' => $request->route,
            'icon' => $request->icon,
            'order' => $request->order,
        ]);

        return redirect()->route('menu')->with('success', 'Menu item created.');
    }

    public function edit(MainMenu $menu) // Type-hinting for route model binding
    {
        return Inertia::render('Menu/Edit', [
            'menuItem' => [
                'id' => $menu->id,
                'name' => $menu->name,
                'route' => $menu->route,
                'icon' => $menu->icon,
                'order' => $menu->order,
            ],
        ]);
    }

    public function update(Request $request, MainMenu $menu) // Type-hinting for route model binding
    {
        $request->validate([
            'name' => ['required', 'max:100'],
            'route' => ['required', 'max:255'],
            'icon' => ['nullable', 'max:50'],
            'order' => ['nullable', 'integer'],
        ]);

        $menu->update($request->only('name', 'route', 'icon', 'order'));

        return redirect()->route('menu')->with('success', 'Menu item updated.');
    }

    public function destroy(MainMenu $menu) // Type-hinting for route model binding
    {
        $menu->delete(); // Assuming soft deletes are not enabled for MainMenu

        return redirect()->route('menu')->with('success', 'Menu item deleted.');
    }

    public function restore($menu) // Assuming soft deletes are not enabled for MainMenu, this method might not be needed or needs adjustment
    {
        // If soft deletes are enabled, you would do something like:
        // MainMenu::onlyTrashed()->findOrFail($menu)->restore();
        return redirect()->route('menu')->with('success', 'Menu item restored.');
    }
}
