<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditRequest;
use App\Models\Branza;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class BranzaController extends Controller
{
    public function index()
    {
        return Inertia::render('Branza/Index', [
            'branzas' => Branza::get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Branza/Create');
    }

    public function store(EditRequest $request)
    {
        Branza::create($request->all());

        return Redirect::route('branza')->with('success', 'Branza dodana.');
    }

    public function edit(Branza $branza)
    {
        return Inertia::render('Branza/Edit', [
            'branza' => [
                'id' => $branza->id,
                'name' => $branza->name,
                'deleted_at' => $branza->deleted_at,
            ],
        ]);
    }

    public function update(EditRequest $request)
    {
        Branza::find($request->id)->update(
            ['name' => $request->name]
        );

        return Redirect::back()->with('success', 'Poprawione.');
    }

    public function destroy(Branza $branza)
    {
        $branza->delete();

        return Redirect::route('branza')->with('success', 'Usunięte.');
    }

    public function restore(Branza $branza)
    {
        $branza->restore();

        return Redirect::back()->with('success', 'Przywrócono.');
    }
}
