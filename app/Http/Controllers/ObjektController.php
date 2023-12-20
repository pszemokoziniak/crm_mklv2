<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditRequest;
use App\Models\Objekt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ObjektController extends Controller
{
    public function index()
    {
        return Inertia::render('Objekt/Index', [
            'objekt' => Objekt::get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Objekt/Create');
    }

    public function store(EditRequest $request)
    {
        Objekt::create($request->all());

        return Redirect::route('objekt')->with('success', 'Objekt dodana.');
    }

    public function edit(Objekt $objekt)
    {
        return Inertia::render('Objekt/Edit', [
            'objekt' => [
                'id' => $objekt->id,
                'name' => $objekt->name,
                'deleted_at' => $objekt->deleted_at,
            ],
        ]);
    }

    public function update(EditRequest $request)
    {
        Objekt::find($request->id)->update(
            ['name' => $request->name]
        );

        return Redirect::route('objekt')->with('success', 'Poprawione.');
    }

    public function destroy(Objekt $objekt)
    {
        $objekt->delete();

        return Redirect::route('objekt')->with('success', 'Usunięte.');
    }

    public function restore(Objekt $objekt)
    {
        $objekt->restore();

        return Redirect::back()->with('success', 'Przywrócono.');
    }
}
