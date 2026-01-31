<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditRequest;
use App\Models\Kraj;
use App\Models\Waluta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class KrajController extends Controller
{
    public function index()
    {
        return Inertia::render('Kraj/Index', [
            'krajs' => Kraj::with('waluta')->get()->map(function ($kraj) {
                return [
                    'id' => $kraj->id,
                    'name' => $kraj->name,
                    'waluta' => $kraj->waluta ? $kraj->waluta->name : '-',
                    'deleted_at' => $kraj->deleted_at,
                ];
            }),
        ]);
    }

    public function create()
    {
        return Inertia::render('Kraj/Create', [
            'walutas' => Waluta::all()->map->only('id', 'name'),
        ]);
    }

    public function store(Request $request)
    {
        Kraj::create([
            'name' => $request->name,
            'waluta_id' => $request->waluta_id,
        ]);

        return Redirect::route('kraj')->with('success', 'Kraj dodany.');
    }

    public function edit(Kraj $kraj)
    {
        return Inertia::render('Kraj/Edit', [
            'kraj' => [
                'id' => $kraj->id,
                'name' => $kraj->name,
                'waluta_id' => $kraj->waluta_id,
                'deleted_at' => $kraj->deleted_at,
            ],
            'walutas' => Waluta::all()->map->only('id', 'name'),
        ]);
    }

    public function update(Request $request)
    {
        Kraj::find($request->id)->update([
            'name' => $request->name,
            'waluta_id' => $request->waluta_id,
        ]);

        return Redirect::route('kraj')->with('success', 'Poprawione.');
    }

    public function destroy(Kraj $kraj)
    {
        $kraj->delete();

        return Redirect::route('kraj')->with('success', 'Usunięte.');
    }

    public function restore(Kraj $kraj)
    {
        $kraj->restore();

        return Redirect::back()->with('success', 'Przywrócono.');
    }
}
