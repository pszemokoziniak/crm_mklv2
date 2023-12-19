<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditRequest;
use App\Models\Zakres;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ZakresController extends Controller
{
    public function index()
    {
        return Inertia::render('Zakres/Index', [
            'zakres' => Zakres::get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Zakres/Create');
    }

    public function store(EditRequest $request)
    {
        Zakres::create($request->all());

        return Redirect::route('zakres')->with('success', 'Zakres dodany.');
    }

    public function edit(Zakres $zakres)
    {
        return Inertia::render('Zakres/Edit', [
            'zakres' => [
                'id' => $zakres->id,
                'name' => $zakres->name,
                'deleted_at' => $zakres->deleted_at,
            ],
        ]);
    }

    public function update(EditRequest $request)
    {
        Zakres::find($request->id)->update(
            ['name' => $request->name]
        );

        return Redirect::route('zakres')->with('success', 'Poprawiono.');
    }

    public function destroy(Zakres $zakres)
    {
        $zakres->delete();

        return Redirect::route('zakres')->with('success', 'Usunięto.');
    }

    public function restore(Zakres $zakres)
    {
        $zakres->restore();

        return Redirect::back()->with('success', 'Przywrócono.');
    }
}
