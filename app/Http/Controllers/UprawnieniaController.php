<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditRequest;
use App\Models\Uprawnienia;
use App\Models\MainMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class UprawnieniaController extends Controller
{
    /**
     * Helper function to safely decode the name if it's a JSON string.
     *
     * @param string $name
     * @return string
     */
    private function getDecodedUprawnienieName(string $name): string
    {
        if (is_string($name) && str_starts_with($name, '{') && str_ends_with($name, '}')) {
            $decoded = json_decode($name, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && isset($decoded['name'])) {
                return $decoded['name'];
            }
        }
        return $name;
    }

    public function index()
    {
        return Inertia::render('Uprawnienia/Index', [
            'uprawnienia' => Uprawnienia::with('mainMenus')
                ->get()
                ->map(function ($uprawnienie) {
                    return [
                        'id' => $uprawnienie->id,
                        'name' => $this->getDecodedUprawnienieName($uprawnienie->name),
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
        $validatedName = $request->validated('name');
        $nameToSave = is_array($validatedName) && isset($validatedName['name']) ? $validatedName['name'] : $validatedName;
        Uprawnienia::create(['name' => $nameToSave]);

        return Redirect::route('uprawnienia')->with('success', 'Uprawnienie dodane.');
    }

    public function edit(Uprawnienia $uprawnienia)
    {
        return Inertia::render('Uprawnienia/Edit', [
            'uprawnienia' => [
                'id' => $uprawnienia->id,
                'name' => $this->getDecodedUprawnienieName($uprawnienia->name),
                'main_menus' => $uprawnienia->mainMenus->pluck('id'),
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

    public function update(Request $request, Uprawnienia $uprawnienia)
    {
        $nameToSave = $request->input('name');
        if (is_array($nameToSave) && isset($nameToSave['name'])) {
            $nameToSave = $nameToSave['name'];
        }
        $uprawnienia->update(['name' => $nameToSave]);

        $mainMenuIds = $request->input('main_menu_ids', []);

        Log::info('Updating Role: ' . $uprawnienia->name . ' (ID: ' . $uprawnienia->id . ')');
        Log::info('Received main_menu_ids for attach: ' . json_encode($mainMenuIds));

        // Detach all existing main menus for this role
        $detachedCount = $uprawnienia->mainMenus()->detach();
        Log::info('Detached ' . $detachedCount . ' existing main menus for role ' . $uprawnienia->name);

        // Attach the new main menus
        $attachedResult = $uprawnienia->mainMenus()->attach($mainMenuIds);
        Log::info('Attached new main menus: ' . json_encode($attachedResult));

        Log::info('Main Menus updated successfully during update.');

        return Redirect::route('uprawnienia')->with('success', 'Poprawiono.');
    }

    public function destroy(Uprawnienia $uprawnienia)
    {
        $uprawnienia->delete();

        return Redirect::route('uprawnienia')->with('success', 'Usunięto.');
    }

    public function restore(Uprawnienia $uprawnienia)
    {
        return Redirect::back()->with('error', 'Przywracanie uprawnień nie jest obecnie wspierane.');
    }
}
